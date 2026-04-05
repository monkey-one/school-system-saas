<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case HADIR = 'hadir';
    case SAKIT = 'sakit';
    case IZIN = 'izin';
    case ALFA = 'alfa';
    case TERLAMBAT = 'terlambat';

    public function label(): string
    {
        return match ($this) {
            self::HADIR => 'Hadir',
            self::SAKIT => 'Sakit',
            self::IZIN => 'Izin',
            self::ALFA => 'Alfa',
            self::TERLAMBAT => 'Terlambat',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::HADIR => 'success',
            self::SAKIT => 'warning',
            self::IZIN => 'info',
            self::ALFA => 'danger',
            self::TERLAMBAT => 'warning',
        };
    }
}
