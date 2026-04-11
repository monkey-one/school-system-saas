<?php

namespace App\Filament\Pages\Auth;

use App\Enums\UserType;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;

// Unified login page shared by all panels. After successful authentication,
// the user is redirected to the correct panel based on their UserType.
// We override authenticate() completely to skip the per-panel canAccessPanel()
// check, because all roles sign in from /school/login and get redirected to
// their own panel afterwards.
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

        session()->regenerate();

        $targetUrl = match ($user->type) {
            UserType::SUPER_ADMIN => url('/super-admin'),
            UserType::TEACHER => url('/teacher'),
            default => url('/school'),
        };

        return new class($targetUrl) implements LoginResponse {
            public function __construct(protected string $url) {}

            public function toResponse($request)
            {
                return redirect($this->url);
            }
        };
    }
}
