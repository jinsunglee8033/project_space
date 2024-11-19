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
//        $schedule->command('inspire')->dailyAt('4:00');
//        $schedule->command('reminder:email')
//            ->timezone('America/New_York')
//            ->dailyAt('8:30');

        $schedule->call('App\Http\Controllers\NotifyController@reminder_email')
            ->timezone('America/New_York')
            ->dailyAt('8:30');

        $schedule->call('App\Http\Controllers\NotifyController@clean_up_projects')
            ->timezone('America/New_York')
            ->dailyAt('8:45');

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
