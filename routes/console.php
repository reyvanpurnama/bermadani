<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule auto-debit simpanan wajib every 1st of the month at 01:00 AM
Schedule::command('simpanan:debit-wajib')->monthlyOn(1, '01:00');
