<?php

namespace App\Filament\Pages\Auth;

use App\Enums\TenantStatus;
use App\Enums\UserType;
use App\Models\Tenant;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

// Unified login page shared by every role. After successful authentication,
// the user is redirected to their own panel or portal based on UserType.
// We override authenticate() completely to skip the per-panel canAccessPanel()
// check, because all roles sign in from /edusaas-admin/login.
class Login extends BaseLogin
{
    protected static string $view = 'filament.login';

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        // Only check that the account is active — do NOT check canAccessPanel()
        // for the current panel, because all roles share this single login page.
        if (! $user || ! $user->is_active) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        if ($user->tenant_id && Tenant::find($user->tenant_id)?->status === TenantStatus::SUSPENDED) {
            Filament::auth()->logout();

            throw ValidationException::withMessages([
                'data.email' => __('This school account is suspended. Please contact the administrator.'),
            ]);
        }

        session()->regenerate();
        session()->forget('impersonate_tenant_id');

        $user->forceFill(['last_login_at' => now()])->saveQuietly();

        $targetUrl = match ($user->type) {
            UserType::SUPER_ADMIN => url('/super-admin'),
            UserType::TEACHER => url('/teacher'),
            UserType::STUDENT => route('student.dashboard'),
            UserType::PARENT => route('parent.dashboard'),
            default => url('/edusaas-admin'),
        };

        // Students and parents often arrive from a deep link (e.g. scanning an
        // attendance QR code), so send them back to the page they wanted.
        $useIntended = in_array($user->type, [UserType::STUDENT, UserType::PARENT], true);

        return new class($targetUrl, $useIntended) implements LoginResponse {
            public function __construct(protected string $url, protected bool $useIntended) {}

            public function toResponse($request)
            {
                return $this->useIntended ? redirect()->intended($this->url) : redirect($this->url);
            }
        };
    }
}
