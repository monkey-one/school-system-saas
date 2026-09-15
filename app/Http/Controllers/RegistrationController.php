<?php

namespace App\Http\Controllers;

use App\Enums\SchoolType;
use App\Enums\TenantStatus;
use App\Enums\UserType;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

// Public self-service sign-up for schools: creates the tenant, its first
// subscription (trial or paid) and the school admin account. Honors the
// "allow registration" and "trial days" settings from System Settings.
class RegistrationController extends Controller
{
    public function create(Request $request)
    {
        $this->ensureRegistrationOpen();

        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $selectedPlan = $request->query('plan');

        return view('registration.index', compact('plans', 'selectedPlan'));
    }

    public function store(Request $request)
    {
        $this->ensureRegistrationOpen();

        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'school_type' => ['required', new Enum(SchoolType::class)],
            'npsn' => 'nullable|string|max:20',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'school_phone' => 'required|string|max:20',
            'school_email' => 'required|email|max:255',
            'principal_name' => 'nullable|string|max:255',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'plan_id' => ['required', Rule::exists('plans', 'id')->where('is_active', true)],
            'billing_cycle' => 'required|in:monthly,annual',
            'registration_type' => 'required|in:trial,paid',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        $trialDays = max(0, (int) Cache::get('system.trial_days', 14));

        return DB::transaction(function () use ($validated, $plan, $trialDays) {
            $slug = Str::slug($validated['school_name']) ?: 'school';
            $originalSlug = $slug;
            $counter = 1;
            while (Tenant::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            $isTrial = $validated['registration_type'] === 'trial';

            $tenant = Tenant::create([
                'name' => $validated['school_name'],
                'slug' => $slug,
                'phone' => $validated['school_phone'],
                'email' => $validated['school_email'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'npsn' => $validated['npsn'],
                'school_type' => $validated['school_type'],
                'principal_name' => $validated['principal_name'],
                'status' => $isTrial ? TenantStatus::TRIAL : TenantStatus::SUSPENDED,
                'trial_ends_at' => $isTrial ? now()->addDays($trialDays) : null,
                'currency' => Cache::get('system.default_currency', 'IDR'),
                'settings' => ['color_primary' => '#1e40af'],
            ]);

            $amount = $validated['billing_cycle'] === 'annual'
                ? (int) $plan->price_annual
                : (int) $plan->price_monthly;

            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'starts_at' => $isTrial ? now() : null,
                'ends_at' => $isTrial ? now()->addDays($trialDays) : null,
                'status' => $isTrial ? 'active' : 'pending',
                'payment_method' => $isTrial ? 'trial' : null,
                'billing_cycle' => $validated['billing_cycle'],
                'payment_amount' => $isTrial ? 0 : $amount,
                'auto_renew' => false,
            ]);

            $tenant->update(['subscription_id' => $subscription->id]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'type' => UserType::SCHOOL_ADMIN,
                'is_active' => $isTrial,
                'email_verified_at' => now(),
            ]);

            // The role table may be empty on a fresh install without seeding.
            rescue(fn () => $user->assignRole('school_admin'), report: false);

            if ($isTrial) {
                return redirect()->route('register.trial-success', [
                    'tenant' => $tenant->slug,
                ]);
            }

            return redirect()->to(URL::temporarySignedRoute('register.payment', now()->addDay(), [
                'subscription' => $subscription->id,
            ]));
        });
    }

    public function payment(Subscription $subscription)
    {
        if ($subscription->status === 'active') {
            return redirect()->route('register.success', [
                'tenant' => $subscription->tenant->slug,
            ]);
        }

        if ($subscription->status !== 'pending') {
            abort(404);
        }

        $tenant = $subscription->tenant;
        $plan = $subscription->plan;
        $amount = (int) $subscription->payment_amount;

        $midtrans = app(MidtransService::class);

        if (! $subscription->payment_token) {
            $orderId = 'SUB-' . $subscription->id . '-' . Str::upper(Str::random(8));
            $token = $midtrans->createSnapToken(
                $orderId,
                $amount,
                [
                    'name' => $tenant->name,
                    'email' => $tenant->email,
                    'phone' => $tenant->phone,
                ],
                [
                    [
                        'id' => 'plan-' . $plan->id,
                        'name' => $plan->name . ' (' . $subscription->billing_cycle . ')',
                        'price' => $amount,
                        'quantity' => 1,
                    ],
                ]
            );

            if ($token) {
                $subscription->update([
                    'payment_reference' => $orderId,
                    'payment_token' => $token,
                ]);
            } else {
                Log::error('Failed to create Midtrans token for subscription', [
                    'subscription_id' => $subscription->id,
                ]);
            }
        }

        return view('registration.payment', [
            'subscription' => $subscription,
            'tenant' => $tenant,
            'plan' => $plan,
            'amount' => $amount,
            'snapToken' => $subscription->payment_token,
            'clientKey' => $midtrans->getClientKey(),
            'isProduction' => $midtrans->isProduction(),
        ]);
    }

    public function success(Request $request)
    {
        $tenant = Tenant::where('slug', (string) $request->query('tenant'))->firstOrFail();

        return view('registration.success', compact('tenant'));
    }

    public function trialSuccess(Request $request)
    {
        $tenant = Tenant::where('slug', (string) $request->query('tenant'))->firstOrFail();

        return view('registration.trial-success', compact('tenant'));
    }

    private function ensureRegistrationOpen(): void
    {
        abort_unless((bool) Cache::get('system.allow_registration', true), 403, __('New school registration is currently closed.'));
    }
}
