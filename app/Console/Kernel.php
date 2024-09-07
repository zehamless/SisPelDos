<?php

namespace App\Console;

use App\Jobs\TerjadwalJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('delete:unused-file')->daily()->timezone('Asia/Jakarta')->runInBackground();
        $schedule->job(new TerjadwalJob())->everyMinute()->timezone('Asia/Jakarta');
        $schedule->command('unpublish:pelatihan')->daily()->timezone('Asia/Jakarta');
        $schedule->command('chatbot')->monthly()->timezone('Asia/Jakarta');

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
