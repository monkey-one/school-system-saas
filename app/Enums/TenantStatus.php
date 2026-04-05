<?php

namespace App\Enums;

enum TenantStatus: string
{
    case TRIAL = 'trial';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::TRIAL => 'Percobaan',
            self::ACTIVE => 'Aktif',
            self::SUSPENDED => 'Ditangguhkan',
            self::EXPIRED => 'Kadaluarsa',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TRIAL => 'info',
            self::ACTIVE => 'success',
            self::SUSPENDED => 'warning',
            self::EXPIRED => 'danger',
        };
    }
}
