<?php

namespace App\Filament\SchoolAdmin\Resources\SavingsTransactionResource\Pages;

use App\Filament\SchoolAdmin\Resources\SavingsTransactionResource;
use App\Models\Student;
use App\Services\SavingsService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSavingsTransaction extends CreateRecord
{
    protected static string $resource = SavingsTransactionResource::class;

    // The running balance is maintained by SavingsService, never by the form.
    protected function handleRecordCreation(array $data): Model
    {
        return app(SavingsService::class)->record(
            Student::findOrFail($data['student_id']),
            $data['type'],
            (float) $data['amount'],
            $data['description'] ?? null,
            $data['merchant'] ?? null,
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
