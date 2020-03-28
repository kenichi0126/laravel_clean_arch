<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        foreach (glob(__DIR__ . '/*') as $val) {
            if (!preg_match('/BaseCommand\\.php/', $val) || !preg_match('/CommandTrait\\.php/', $val)) {
                $this->load($val);
            }
        }

        require base_path('routes/console.php');
    }
}
