<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftar Artisan commands kustom kamu.
     *
     * @var array
     */
    protected $commands = [
        // Daftarkan command kustommu di sini
        \App\Console\Commands\ComputeTopSeller::class,
        \App\Console\Commands\UpdateDonationStatus::class,
    ];

    /**
     * Setup the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Jalankan setiap tanggal 1 jam 00:05 pagi
        $schedule->command('compute:topseller')
            ->monthlyOn(1, '00:05')
            ->withoutOverlapping()   // mencegah tumpang tindih eksekusi
            ->runInBackground();     // opsional: jalankan di background

        // Jalankan tiap tengah malam
        $schedule->command('barang:update-donation-status')
            ->dailyAt('00:05')
            ->description('Update status donation untuk barang yang lewat 7 hari setelah TGL_KELUAR');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
