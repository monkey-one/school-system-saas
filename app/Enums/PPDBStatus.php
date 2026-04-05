<?php

namespace App\Enums;

enum PPDBStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case WAITLIST = 'waitlist';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::ACCEPTED => 'Diterima',
            self::REJECTED => 'Ditolak',
            self::WAITLIST => 'Cadangan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::ACCEPTED => 'success',
            self::REJECTED => 'danger',
            self::WAITLIST => 'info',
        };
    }
}
