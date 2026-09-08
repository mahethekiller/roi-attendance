<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatically sync biometric attendances every minute
Schedule::command('attendance:sync-biometric --days=3')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
