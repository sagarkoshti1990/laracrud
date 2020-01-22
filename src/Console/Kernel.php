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
        // CRUD files:
        'App\Console\Commands\CrudModelCommand',
        'App\Console\Commands\CrudControllerCommand',
        'App\Console\Commands\CrudCommand',
        'App\Console\Commands\CrudViewIndexCommand',
        'App\Console\Commands\CrudViewCreateCommand',
        'App\Console\Commands\CrudViewEditCommand',
        'App\Console\Commands\CrudViewShowCommand',
        'App\Console\Commands\CrudMigrateCommand',
        'App\Console\Commands\ConfigActivityLogsCommand',
        'App\Console\Commands\DailyReport',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('dailyreport')->everyMinute();
        
    }
    
    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
