<?php

namespace App\Enums;

// Represents the method used to make a payment
enum PaymentMethod: string
{
    case CASH = 'cash';
    case TRANSFER = 'transfer';
    case MIDTRANS = 'midtrans';
    case XENDIT = 'xendit';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Tunai',
            self::TRANSFER => 'Transfer Bank',
            self::MIDTRANS => 'Midtrans',
            self::XENDIT => 'Xendit',
        };
    }
}
