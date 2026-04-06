<?php

namespace App\Enums;

// Represents the enrollment status of a student
enum StudentStatus: string
{
    case ACTIVE = 'active';
    case ALUMNI = 'alumni';
    case TRANSFERRED = 'transferred';
    case DROPPED = 'dropped';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::ALUMNI => 'Alumni',
            self::TRANSFERRED => 'Pindah',
            self::DROPPED => 'Keluar',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::ALUMNI => 'info',
            self::TRANSFERRED => 'warning',
            self::DROPPED => 'danger',
        };
    }
}
