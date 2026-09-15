<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public Demo Mode
    |--------------------------------------------------------------------------
    |
    | Enable ONLY on a public showcase installation. When enabled:
    |  - demo login credentials are listed on the login page,
    |  - a small call-to-action linking to the author is shown,
    |  - account credentials cannot be changed and core records cannot be
    |    deleted (so visitors cannot lock each other out),
    |  - system settings are read-only, WhatsApp broadcasts and file uploads
    |    by students are disabled,
    |  - `php artisan edusaas:demo-reset` restores the seeded data on the
    |    schedule below.
    |
    | Keep this disabled on real schools' installations.
    |
    */

    'enabled' => (bool) env('DEMO_MODE', false),

    // Cron expression for the automatic data reset (default: every 6 hours).
    'reset_cron' => env('DEMO_RESET_CRON', '0 */6 * * *'),

    // Accounts created by DemoSeeder, listed on the login page in demo mode.
    'password' => 'password',

    'accounts' => [
        'Super Admin' => 'superadmin@edusaas.id',
        'School Admin' => 'admin@smpn1demo.id',
        'Operator' => 'operator@smpn1demo.id',
        'Teacher' => 'guru@smpn1demo.id',
        'Student' => 'siswa@smpn1demo.id',
        'Parent' => 'ortu@smpn1demo.id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Author / Developer Credit
    |--------------------------------------------------------------------------
    |
    | Shown as "Developed by" on public pages. Set APP_AUTHOR_NAME to an empty
    | value to hide the credit.
    |
    */

    'author' => [
        'name' => env('APP_AUTHOR_NAME', 'PT Danum Inovasi Teknologi'),
        'brand' => env('APP_AUTHOR_BRAND', 'numintek'),
        'url' => env('APP_AUTHOR_URL', 'https://numintek.com'),
        'whatsapp' => env('APP_AUTHOR_WHATSAPP', '6285220091770'),
    ],

];
