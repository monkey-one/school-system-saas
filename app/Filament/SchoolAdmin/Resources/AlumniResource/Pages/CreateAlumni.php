<?php

namespace App\Filament\SchoolAdmin\Resources\AlumniResource\Pages;

use App\Filament\SchoolAdmin\Resources\AlumniResource;
use App\Models\Tenant;
use Filament\Resources\Pages\CreateRecord;

class CreateAlumni extends CreateRecord
{
    protected static string $resource = AlumniResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = Tenant::current()?->id;
        return $data;
    }
}
