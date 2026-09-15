<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

// Lets a super admin open a school's admin panel ("Enter Panel"). The chosen
// school is kept in the session so every follow-up request, including
// Livewire AJAX calls, resolves the same tenant (see ResolveTenant).
class ImpersonationController extends Controller
{
    public function start(Request $request, Tenant $tenant)
    {
        $request->session()->put('impersonate_tenant_id', $tenant->id);

        return redirect()->to(url('/edusaas-admin'));
    }

    public function stop(Request $request)
    {
        $request->session()->forget('impersonate_tenant_id');

        return redirect()->to(url('/super-admin/tenants'));
    }
}
