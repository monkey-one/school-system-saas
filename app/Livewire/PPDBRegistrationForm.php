<?php

namespace App\Livewire;

use App\Enums\PPDBStatus;
use App\Models\PPDBRegistration;
use App\Models\PPDBWave;
use App\Models\Tenant;
use Livewire\Component;

class PPDBRegistrationForm extends Component
{
    public int $currentStep = 1;

    // Step 1: Wave selection
    public ?int $ppdb_wave_id = null;

    // Step 2: Student data
    public string $full_name = '';
    public string $birth_date = '';
    public string $gender = '';
    public string $previous_school = '';
    public string $address = '';

    // Step 3: Parent data
    public string $parent_name = '';
    public string $parent_phone = '';
    public string $parent_email = '';

    // Result
    public ?string $registrationNumber = null;
    public bool $submitted = false;

    public function nextStep(): void
    {
        $this->validateCurrentStep();
        $this->currentStep++;
    }

    public function previousStep(): void
    {
        $this->currentStep--;
    }

    public function submit(): void
    {
        $this->validateCurrentStep();

        $tenant = Tenant::current();

        $count = PPDBRegistration::where('tenant_id', $tenant->id)
            ->whereYear('created_at', now()->year)
            ->count();

        $this->registrationNumber = sprintf('PPDB-%d-%05d', now()->year, $count + 1);

        PPDBRegistration::create([
            'tenant_id' => $tenant->id,
            'ppdb_wave_id' => $this->ppdb_wave_id,
            'registration_number' => $this->registrationNumber,
            'full_name' => $this->full_name,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'parent_name' => $this->parent_name,
            'parent_phone' => $this->parent_phone,
            'parent_email' => $this->parent_email ?: null,
            'previous_school' => $this->previous_school ?: null,
            'address' => $this->address,
            'status' => PPDBStatus::PENDING,
        ]);

        $this->submitted = true;
    }

    protected function validateCurrentStep(): void
    {
        match ($this->currentStep) {
            1 => $this->validate([
                'ppdb_wave_id' => 'required|exists:ppdb_waves,id',
            ]),
            2 => $this->validate([
                'full_name' => 'required|string|max:255',
                'birth_date' => 'required|date|before:today',
                'gender' => 'required|in:male,female',
                'address' => 'required|string|max:1000',
                'previous_school' => 'nullable|string|max:255',
            ]),
            3 => $this->validate([
                'parent_name' => 'required|string|max:255',
                'parent_phone' => 'required|string|max:20',
                'parent_email' => 'nullable|email|max:255',
            ]),
            default => null,
        };
    }

    public function render()
    {
        $waves = PPDBWave::where('tenant_id', Tenant::current()->id)
            ->where('is_active', true)
            ->where('closes_at', '>=', now())
            ->get();

        return view('livewire.ppdb-registration-form', compact('waves'));
    }
}
