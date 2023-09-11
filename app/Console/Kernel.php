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
        'App\Console\Commands\ClearUserUsages',
        'App\Console\Commands\ClearUnverifiedUsersCommand',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cron:clear-user-usages')->monthly();
        $schedule->command('cron:clear-unverified-users')->dailyAt('03:00');
        $schedule->command('cache:clear')->weeklyOn(0, '4:00');
        $schedule->command('view:clear')->weeklyOn(0, '5:00');
        $schedule->command('auth:clear-resets')->weeklyOn(0, '6:00');
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


/*
 
    The given code snippet is defining Laravel's console kernel. It sets up scheduling for different Artisan commands and provides details about the custom console commands your application is offering.

    Here's what's happening:

    protected $commands: An array that lists the classes for the custom Artisan commands you've created. In this case, it includes the commands to clear user usages and unverified users.

    protected function schedule(Schedule $schedule): This method sets up the scheduling for various console commands:

    cron:clear-user-usages is scheduled to run monthly.
    cron:clear-unverified-users is scheduled to run daily at 3:00 AM.
    cache:clear is scheduled to run weekly on Sunday at 4:00 AM.
    view:clear is scheduled to run weekly on Sunday at 5:00 AM.
    auth:clear-resets is scheduled to run weekly on Sunday at 6:00 AM.
    These scheduled commands help in automating certain maintenance tasks, such as clearing unverified users, resetting monthly user usage counters, and clearing various caches.

    protected function commands(): This method registers the commands for the application. It loads all the command classes from the specified directory and also requires the console routes file.
    Overall, this kernel class plays a crucial role in defining and scheduling console tasks for your Laravel application. It organizes and controls how these commands are loaded and when they are executed.

*/