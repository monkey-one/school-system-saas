<?php

namespace App\Enums;

// Represents the current status of a billing payment
enum PaymentStatus: string
{
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case WAIVED = 'waived';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Belum Bayar',
            self::PARTIAL => 'Bayar Sebagian',
            self::PAID => 'Lunas',
            self::OVERDUE => 'Jatuh Tempo',
            self::WAIVED => 'Dibebaskan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'danger',
            self::PARTIAL => 'warning',
            self::PAID => 'success',
            self::OVERDUE => 'danger',
            self::WAIVED => 'gray',
        };
    }
}
