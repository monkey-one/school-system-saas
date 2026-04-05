<?php

namespace App\Livewire;

use App\Models\PPDBRegistration;
use App\Models\Tenant;
use Livewire\Component;

class PPDBStatusCheck extends Component
{
    public string $registrationNumber = '';
    public ?PPDBRegistration $registration = null;

    public function check(): void
    {
        $this->validate([
            'registrationNumber' => 'required|string',
        ]);

        $this->registration = PPDBRegistration::where('registration_number', $this->registrationNumber)
            ->where('tenant_id', Tenant::current()->id)
            ->first();

        if (! $this->registration) {
            $this->addError('registrationNumber', 'Nomor pendaftaran tidak ditemukan.');
        }
    }

    public function render()
    {
        return view('livewire.ppdb-status-check');
    }
}
