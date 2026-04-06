<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class MiscController extends Controller
{
    public function switchLocale(string $locale)
    {
        if (in_array($locale, ['id', 'en'])) {
            session()->put('locale', $locale);
        }

        return redirect()->back();
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
