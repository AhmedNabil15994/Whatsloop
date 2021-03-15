<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\GroupMsg;

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

        $tenants = \DB::table('tenants')->get();
        foreach($tenants as $tenant){
            $schedule->command('tenants:run groupMsg:send --tenants='.$tenant->id)->everyMinute()->withoutOverlapping();
            $schedule->command('tenants:run instance:status --tenants='.$tenant->id)->everyFiveMinutes()->withoutOverlapping();
        }

        $schedule->command('queue:work')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:restart')->hourly()->withoutOverlapping();
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
