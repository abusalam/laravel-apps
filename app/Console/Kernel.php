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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->daily()
        //          ->sendOutputTo(storage_path('logs/inspire.log'))
        //          ->emailWrittenOutputTo(env('ADMIN_EMAIL'));
        // $schedule->command('sms:collect')
        //     ->withoutOverlapping()
        //     ->sendOutputTo(storage_path('logs/sms-collect.log'));
        $schedule->command('php:info')
            ->withoutOverlapping()
            ->sendOutputTo(storage_path('logs/php-info.log'))
            ->emailWrittenOutputTo(env('ADMIN_EMAIL'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
