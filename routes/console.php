<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send SPP reminders every Monday at 8am
Schedule::command('edusaas:send-spp-reminders')->weeklyOn(1, '08:00');

// Generate monthly bills on the 1st of each month at 00:30
Schedule::command('edusaas:generate-monthly-bills')->monthlyOn(1, '00:30');
