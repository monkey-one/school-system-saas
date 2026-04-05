<?php

namespace App\Enums;

enum SubjectType: string
{
    case TEORI = 'teori';
    case PRAKTEK = 'praktek';
    case MUATAN_LOKAL = 'muatan_lokal';

    public function label(): string
    {
        return match ($this) {
            self::TEORI => 'Teori',
            self::PRAKTEK => 'Praktek',
            self::MUATAN_LOKAL => 'Muatan Lokal',
        };
    }
}
