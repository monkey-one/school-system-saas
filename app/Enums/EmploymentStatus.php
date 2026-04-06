<?php

namespace App\Enums;

// Represents the employment classification of a teacher or staff member
enum EmploymentStatus: string
{
    case PNS = 'PNS';
    case GTT = 'GTT';
    case GTY = 'GTY';
    case HONORER = 'Honorer';

    public function label(): string
    {
        return match ($this) {
            self::PNS => 'PNS',
            self::GTT => 'Guru Tidak Tetap',
            self::GTY => 'Guru Tetap Yayasan',
            self::HONORER => 'Honorer',
        };
    }
}
