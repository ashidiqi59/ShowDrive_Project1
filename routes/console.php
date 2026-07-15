<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================
// SCHEDULED TASKS
// Untuk mengaktifkan di server, tambahkan cron berikut:
//   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
// ============================================================

// Auto-cancel booking Unpaid yang belum ada pembayaran dalam 24 jam
// Dijalankan setiap jam — memastikan slot unit tidak terkunci selamanya
Schedule::command('bookings:cancel-expired')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();

// Backup seluruh file upload ke direktori backup terpisah
// Dijalankan setiap Minggu pukul 02:00 WIB (off-peak hours)
Schedule::command('backup:storage')
    ->weeklyOn(0, '02:00')
    ->withoutOverlapping()
    ->runInBackground();
