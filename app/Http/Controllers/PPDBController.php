<?php

namespace App\Http\Controllers;

use App\Enums\PPDBStatus;
use App\Models\PPDBRegistration;
use App\Models\PPDBWave;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PPDBController extends Controller
{
    public function index()
    {
        $tenant = Tenant::current();

        $waves = PPDBWave::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('closes_at', '>=', now())
            ->with('academicYear')
            ->orderBy('opens_at')
            ->get();

        return view('ppdb.index', compact('waves', 'tenant'));
    }

    public function register(PPDBWave $wave)
    {
        abort_unless($wave->is_active && $wave->closes_at->isFuture(), 404);

        return view('ppdb.register', compact('wave'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ppdb_wave_id' => 'required|exists:ppdb_waves,id',
            'full_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',
            'parent_email' => 'nullable|email|max:255',
            'previous_school' => 'nullable|string|max:255',
            'address' => 'required|string|max:1000',
        ]);

        $tenant = Tenant::current();

        $count = PPDBRegistration::where('tenant_id', $tenant->id)
            ->whereYear('created_at', now()->year)
            ->count();

        $registrationNumber = sprintf('PPDB-%d-%05d', now()->year, $count + 1);

        $registration = PPDBRegistration::create([
            'tenant_id' => $tenant->id,
            'ppdb_wave_id' => $validated['ppdb_wave_id'],
            'registration_number' => $registrationNumber,
            'full_name' => $validated['full_name'],
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'parent_name' => $validated['parent_name'],
            'parent_phone' => $validated['parent_phone'],
            'parent_email' => $validated['parent_email'] ?? null,
            'previous_school' => $validated['previous_school'] ?? null,
            'address' => $validated['address'],
            'status' => PPDBStatus::PENDING,
        ]);

        return redirect()->route('ppdb.status')
            ->with('success', 'Pendaftaran berhasil! Nomor pendaftaran Anda: ' . $registrationNumber);
    }

    public function status()
    {
        return view('ppdb.status');
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string',
        ]);

        $registration = PPDBRegistration::where('registration_number', $request->registration_number)
            ->where('tenant_id', Tenant::current()->id)
            ->first();

        return view('ppdb.status', compact('registration'));
    }

    public function acceptanceLetter(int $id)
    {
        $registration = PPDBRegistration::where('id', $id)
            ->where('tenant_id', Tenant::current()->id)
            ->where('status', PPDBStatus::ACCEPTED)
            ->firstOrFail();

        $tenant = Tenant::current();

        $pdf = Pdf::loadView('ppdb.acceptance-letter', compact('registration', 'tenant'));

        return $pdf->download('surat-penerimaan-' . $registration->registration_number . '.pdf');
    }
}
