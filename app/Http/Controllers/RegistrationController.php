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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;

class RegistrationController extends Controller
{
    public function create(Request $request)
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $selectedPlan = $request->query('plan');

        return view('registration.index', compact('plans', 'selectedPlan'));
    }

    public function store(Request $request)
    {
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
            'admin_password' => 'required|string|min:8|confirmed',
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,annual',
            'registration_type' => 'required|in:trial,paid',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);

        return DB::transaction(function () use ($validated, $plan, $request) {
            $slug = Str::slug($validated['school_name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Tenant::where('slug', $slug)->exists()) {
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
                'trial_ends_at' => $isTrial ? now()->addDays(14) : null,
                'settings' => ['color_primary' => '#1e40af'],
            ]);

            $amount = $validated['billing_cycle'] === 'annual'
                ? (int) $plan->price_annual
                : (int) $plan->price_monthly;

            $startsAt = $isTrial ? now() : null;
            $endsAt = $isTrial
                ? now()->addDays(14)
                : null;

            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
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

            if ($user->hasRole === null || ! method_exists($user, 'assignRole')) {
                // HasRoles trait may not be loaded, skip
            } else {
                try {
                    $user->assignRole('school_admin');
                } catch (\Throwable) {
                    // Role may not exist yet
                }
            }

            if ($isTrial) {
                return redirect()->route('register.trial-success', [
                    'tenant' => $tenant->slug,
                ]);
            }

            return redirect()->route('register.payment', [
                'subscription' => $subscription->id,
            ]);
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
            $orderId = 'SUB-' . $subscription->id . '-' . time();
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
        $tenantSlug = $request->query('tenant');
        $tenant = Tenant::where('slug', $tenantSlug)->firstOrFail();

        return view('registration.success', compact('tenant'));
    }

    public function trialSuccess(Request $request)
    {
        $tenantSlug = $request->query('tenant');
        $tenant = Tenant::where('slug', $tenantSlug)->firstOrFail();

        return view('registration.trial-success', compact('tenant'));
    }
}
