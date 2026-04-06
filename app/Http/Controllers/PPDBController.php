<?php

namespace App\Http\Controllers;

use App\Enums\PPDBStatus;
use App\Models\PPDBRegistration;
use App\Models\PPDBWave;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Handles all public PPDB (student admission) pages: browsing open waves,
// submitting a registration form, checking application status, and
// downloading the acceptance letter PDF.
class PPDBController extends Controller
{
    // Returns the current tenant or aborts with 404 when no tenant is resolved.
    // Every PPDB route sits behind the ResolveTenant middleware, but we add
    // an explicit guard here so a missing tenant never causes a null-access crash.
    private function tenant(): Tenant
    {
        $tenant = Tenant::current();
        abort_unless($tenant, 404, 'Tenant not resolved.');
        return $tenant;
    }

    // Show all currently open admission waves for the resolved tenant.
    public function index()
    {
        $tenant = $this->tenant();

        $waves = PPDBWave::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('closes_at', '>=', now())
            ->with('academicYear')
            ->orderBy('opens_at')
            ->get();

        return view('ppdb.index', compact('waves', 'tenant'));
    }

    // Show the multi-step registration form for a specific wave.
    public function register(PPDBWave $wave)
    {
        abort_unless($wave->is_active && $wave->closes_at->isFuture(), 404);

        return view('ppdb.register', compact('wave'));
    }

    // Validate and persist a new registration submission.
    // The registration number is generated inside a DB lock to prevent
    // race conditions under concurrent requests.
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

        $tenant = $this->tenant();

        // Wrap in a transaction with a lock to avoid duplicate registration numbers
        // when two applicants submit at the same time.
        $registration = DB::transaction(function () use ($validated, $tenant) {
            $count = PPDBRegistration::where('tenant_id', $tenant->id)
                ->whereYear('created_at', now()->year)
                ->lockForUpdate()
                ->count();

            $registrationNumber = sprintf('PPDB-%d-%05d', now()->year, $count + 1);

            return PPDBRegistration::create([
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
        });

        return redirect()->route('ppdb.status')
            ->with('success', 'Pendaftaran berhasil! Nomor pendaftaran Anda: ' . $registration->registration_number);
    }

    // Show the status lookup form (and result if a search was already performed).
    public function status()
    {
        return view('ppdb.status');
    }

    // Look up a registration by its number within the current tenant scope.
    public function checkStatus(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string',
        ]);

        $tenant = $this->tenant();

        $registration = PPDBRegistration::where('registration_number', $request->registration_number)
            ->where('tenant_id', $tenant->id)
            ->first();

        return view('ppdb.status', compact('registration'));
    }

    // Generate and download the acceptance letter as a PDF.
    // Only accepted registrations belonging to the current tenant are allowed.
    public function acceptanceLetter(int $id)
    {
        $tenant = $this->tenant();

        $registration = PPDBRegistration::where('id', $id)
            ->where('tenant_id', $tenant->id)
            ->where('status', PPDBStatus::ACCEPTED)
            ->firstOrFail();

        $pdf = Pdf::loadView('ppdb.acceptance-letter', compact('registration', 'tenant'));

        return $pdf->download('surat-penerimaan-' . $registration->registration_number . '.pdf');
    }
}
