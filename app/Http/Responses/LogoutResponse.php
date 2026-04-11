<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

// Always redirect to the unified login page after logout, regardless of which
// panel the user was in. This replaces Filament's default LogoutResponse which
// tries to resolve the current panel's login route (which may not exist for
// panels that don't have their own login page).
class LogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        return redirect()->to(url('/edusaas-admin/login'));
    }
}
