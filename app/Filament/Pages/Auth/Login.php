<?php

namespace App\Filament\Pages\Auth;

use App\Enums\UserType;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;

// Unified login page shared by all panels. After successful authentication,
// the user is redirected to the correct panel based on their UserType.
class Login extends BaseLogin
{
    protected static string $view = 'filament.login';

    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        // If parent returned null, authentication failed — just bail out.
        if ($response === null) {
            return null;
        }

        $user = auth()->user();

        if (! $user) {
            return $response;
        }

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
