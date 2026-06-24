<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled backup setiap tengah malam (opsional, jalankan dengan Task Scheduler Windows)
Schedule::command('backup:daily')->dailyAt('23:59');
