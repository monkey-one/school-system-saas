<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

// Custom login page that overrides the default Filament login view to show
// the school branding and panel-specific subtitle.
class Login extends BaseLogin
{
    protected static string $view = 'filament.login';
}
