<?php

namespace App\Enums;

// Represents the gender of a user
enum Gender: string
{
    case MALE = 'L';
    case FEMALE = 'P';

    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Laki-laki',
            self::FEMALE => 'Perempuan',
        };
    }
}
