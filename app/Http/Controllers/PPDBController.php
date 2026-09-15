<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Enums\PPDBStatus;
use App\Models\PPDBRegistration;
use App\Models\PPDBWave;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// Public PPDB (new student admission): open waves, online registration with
// document upload, registration proof, status check and acceptance letter.
// Uploaded documents are stored on the private disk and are only served to
// school staff (see PPDBDocumentController). Result pages use signed URLs so
// applicants' data cannot be enumerated.
class PPDBController extends Controller
{
    private function tenant(): Tenant
    {
        $tenant = Tenant::current();
        abort_unless($tenant, 404);

        return $tenant;
    }

    // Document key => [label, required, allowed mimes].
    public static function documents(): array
    {
        return [
            'birth_certificate' => [__('Birth certificate'), true, 'jpg,jpeg,png,pdf'],
            'family_card' => [__('Family card (KK)'), true, 'jpg,jpeg,png,pdf'],
            'photo' => [__('Passport photo 3x4'), true, 'jpg,jpeg,png'],
            'diploma' => [__('Diploma / graduation letter (SKL)'), false, 'jpg,jpeg,png,pdf'],
        ];
    }

    public function index(): View
    {
        return view('ppdb.index', [
            'tenant' => $this->tenant(),
            'waves' => PPDBWave::where('is_active', true)
                ->where('closes_at', '>=', now())
                ->with('academicYear')
                ->withCount('registrations')
                ->orderBy('opens_at')
                ->get(),
        ]);
    }

    public function register(PPDBWave $wave): View
    {
        abort_unless($this->isOpen($wave), 404);

        return view('ppdb.register', [
            'tenant' => $this->tenant(),
            'wave' => $wave->load('academicYear'),
            'documents' => self::documents(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->tenant();

        $rules = [
            'ppdb_wave_id' => ['required', Rule::exists('ppdb_waves', 'id')
                ->where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->whereNull('deleted_at')],
            'full_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today', 'after:' . now()->subYears(25)->toDateString()],
            'gender' => ['required', Rule::enum(Gender::class)],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'parent_name' => ['required', 'string', 'max:255'],
            'parent_phone' => ['required', 'string', 'regex:/^[0-9+\-\s()]{8,20}$/'],
            'parent_email' => ['nullable', 'email', 'max:255'],
            'agreement' => ['accepted'],
        ];

        foreach (self::documents() as $key => [$label, $required, $mimes]) {
            $rules["documents.{$key}"] = [$required ? 'required' : 'nullable', 'file', "mimes:{$mimes}", 'max:2048'];
        }

        $validated = $request->validate($rules, [], collect(self::documents())->mapWithKeys(fn ($doc, $key) => ["documents.{$key}" => $doc[0]])->all());

        $wave = PPDBWave::findOrFail($validated['ppdb_wave_id']);
        abort_unless($this->isOpen($wave), 422, __('Registration for this wave is not open.'));

        $paths = [];
        foreach (array_keys(self::documents()) as $key) {
            if ($file = $request->file("documents.{$key}")) {
                // Random file name + extension derived from the verified MIME type.
                $paths[$key] = $file->store("ppdb/{$tenant->id}", 'local');
            }
        }

        // The number contains the school ID because the column is unique across
        // all schools; the count is locked so concurrent submissions never collide.
        $registration = DB::transaction(function () use ($validated, $tenant, $wave, $paths) {
            $count = PPDBRegistration::withTrashed()
                ->where('tenant_id', $tenant->id)
                ->whereYear('created_at', now()->year)
                ->lockForUpdate()
                ->count();

            return PPDBRegistration::create([
                'tenant_id' => $tenant->id,
                'ppdb_wave_id' => $wave->id,
                'registration_number' => sprintf('PPDB-%d-%d-%05d', now()->year, $tenant->id, $count + 1),
                'full_name' => $validated['full_name'],
                'birth_date' => $validated['birth_date'],
                'gender' => $validated['gender'],
                'parent_name' => $validated['parent_name'],
                'parent_phone' => $validated['parent_phone'],
                'parent_email' => $validated['parent_email'] ?? null,
                'previous_school' => $validated['previous_school'] ?? null,
                'address' => $validated['address'],
                'documents' => $paths,
                'status' => PPDBStatus::PENDING,
            ]);
        });

        return redirect()->to(URL::temporarySignedRoute('ppdb.success', now()->addDays(30), ['registration' => $registration->id]));
    }

    public function success(PPDBRegistration $registration): View
    {
        return view('ppdb.success', [
            'tenant' => $this->tenant(),
            'registration' => $registration->load('ppdbWave'),
            'proofUrl' => URL::temporarySignedRoute('ppdb.proof', now()->addDays(30), ['registration' => $registration->id]),
        ]);
    }

    public function proof(PPDBRegistration $registration)
    {
        return Pdf::loadView('pdf.ppdb-proof', [
            'registration' => $registration->load('ppdbWave.academicYear'),
            'tenant' => $this->tenant(),
            'documents' => self::documents(),
        ])->download('bukti-pendaftaran-' . $registration->registration_number . '.pdf');
    }

    public function status(): View
    {
        return view('ppdb.status', ['tenant' => $this->tenant(), 'registration' => null, 'searched' => false, 'acceptanceUrl' => null]);
    }

    // Lookup requires both the registration number and the date of birth.
    public function checkStatus(Request $request): View
    {
        $data = $request->validate([
            'registration_number' => ['required', 'string', 'max:50'],
            'birth_date' => ['required', 'date'],
        ]);

        $registration = PPDBRegistration::with('ppdbWave')
            ->where('registration_number', strtoupper(trim($data['registration_number'])))
            ->whereDate('birth_date', $data['birth_date'])
            ->first();

        return view('ppdb.status', [
            'tenant' => $this->tenant(),
            'registration' => $registration,
            'searched' => true,
            'acceptanceUrl' => $registration?->status === PPDBStatus::ACCEPTED
                ? URL::temporarySignedRoute('ppdb.acceptance-letter', now()->addDays(7), ['registration' => $registration->id])
                : null,
        ]);
    }

    public function acceptanceLetter(PPDBRegistration $registration)
    {
        abort_unless($registration->status === PPDBStatus::ACCEPTED, 404);

        return Pdf::loadView('pdf.ppdb-acceptance', [
            'registration' => $registration->load('ppdbWave.academicYear'),
            'tenant' => $this->tenant(),
        ])->download('surat-penerimaan-' . $registration->registration_number . '.pdf');
    }

    private function isOpen(PPDBWave $wave): bool
    {
        return $wave->is_active && $wave->opens_at?->isPast() && $wave->closes_at?->isFuture();
    }
}
