<?php

namespace App\Enums;

enum SchoolType: string
{
    case SD = 'SD';
    case SMP = 'SMP';
    case SMA = 'SMA';
    case SMK = 'SMK';
    case MI = 'MI';
    case MTS = 'MTs';
    case MA = 'MA';

    public function label(): string
    {
        return match ($this) {
            self::SD => 'SD',
            self::SMP => 'SMP',
            self::SMA => 'SMA',
            self::SMK => 'SMK',
            self::MI => 'MI',
            self::MTS => 'MTs',
            self::MA => 'MA',
        };
    }
}
