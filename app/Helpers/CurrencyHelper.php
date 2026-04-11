<?php

namespace App\Helpers;

use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;

class CurrencyHelper
{
    protected static array $currencies = [
        'IDR' => ['symbol' => 'Rp', 'name' => 'Indonesian Rupiah', 'decimals' => 0, 'dec_sep' => ',', 'thou_sep' => '.'],
        'USD' => ['symbol' => '$', 'name' => 'US Dollar', 'decimals' => 2, 'dec_sep' => '.', 'thou_sep' => ','],
        'EUR' => ['symbol' => '€', 'name' => 'Euro', 'decimals' => 2, 'dec_sep' => ',', 'thou_sep' => '.'],
        'MYR' => ['symbol' => 'RM', 'name' => 'Malaysian Ringgit', 'decimals' => 2, 'dec_sep' => '.', 'thou_sep' => ','],
        'SGD' => ['symbol' => 'S$', 'name' => 'Singapore Dollar', 'decimals' => 2, 'dec_sep' => '.', 'thou_sep' => ','],
        'GBP' => ['symbol' => '£', 'name' => 'British Pound', 'decimals' => 2, 'dec_sep' => '.', 'thou_sep' => ','],
        'JPY' => ['symbol' => '¥', 'name' => 'Japanese Yen', 'decimals' => 0, 'dec_sep' => '.', 'thou_sep' => ','],
        'AUD' => ['symbol' => 'A$', 'name' => 'Australian Dollar', 'decimals' => 2, 'dec_sep' => '.', 'thou_sep' => ','],
        'THB' => ['symbol' => '฿', 'name' => 'Thai Baht', 'decimals' => 2, 'dec_sep' => '.', 'thou_sep' => ','],
        'PHP' => ['symbol' => '₱', 'name' => 'Philippine Peso', 'decimals' => 2, 'dec_sep' => '.', 'thou_sep' => ','],
        'INR' => ['symbol' => '₹', 'name' => 'Indian Rupee', 'decimals' => 2, 'dec_sep' => '.', 'thou_sep' => ','],
        'BRL' => ['symbol' => 'R$', 'name' => 'Brazilian Real', 'decimals' => 2, 'dec_sep' => ',', 'thou_sep' => '.'],
    ];

    public static function code(): string
    {
        $tenant = Tenant::current();
        if ($tenant && !empty($tenant->currency)) {
            return $tenant->currency;
        }

        return Cache::get('system.default_currency', 'IDR');
    }

    public static function symbol(?string $code = null): string
    {
        $code = $code ?? static::code();

        return static::$currencies[$code]['symbol'] ?? $code;
    }

    public static function format(float|int|null $amount, ?string $code = null): string
    {
        $code = $code ?? static::code();
        $currency = static::$currencies[$code] ?? static::$currencies['IDR'];

        $formatted = number_format(
            (float) ($amount ?? 0),
            $currency['decimals'],
            $currency['dec_sep'],
            $currency['thou_sep']
        );

        return $currency['symbol'] . ' ' . $formatted;
    }

    public static function formatNumber(float|int|null $amount, ?string $code = null): string
    {
        $code = $code ?? static::code();
        $currency = static::$currencies[$code] ?? static::$currencies['IDR'];

        return number_format(
            (float) ($amount ?? 0),
            $currency['decimals'],
            $currency['dec_sep'],
            $currency['thou_sep']
        );
    }

    public static function options(): array
    {
        return collect(static::$currencies)->mapWithKeys(fn ($data, $code) => [
            $code => "{$code} - {$data['name']} ({$data['symbol']})",
        ])->toArray();
    }

    public static function decimals(?string $code = null): int
    {
        $code = $code ?? static::code();

        return static::$currencies[$code]['decimals'] ?? 2;
    }
}
