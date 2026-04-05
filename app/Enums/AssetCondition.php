<?php

namespace App\Enums;

enum AssetCondition: string
{
    case GOOD = 'good';
    case MINOR_DAMAGE = 'minor_damage';
    case MAJOR_DAMAGE = 'major_damage';
    case LOST = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::GOOD => 'Baik',
            self::MINOR_DAMAGE => 'Rusak Ringan',
            self::MAJOR_DAMAGE => 'Rusak Berat',
            self::LOST => 'Hilang',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::GOOD => 'success',
            self::MINOR_DAMAGE => 'warning',
            self::MAJOR_DAMAGE => 'danger',
            self::LOST => 'gray',
        };
    }
}
