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
        \App\Console\Commands\DelayedGroupMessages::class,
        \App\Console\Commands\InstanceStatus::class,
        \App\Console\Commands\SyncMessages::class,
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
            $schedule->command('tenants:run groupMsg:send --tenants='.$tenant->id)->everyMinute();
            $schedule->command('tenants:run instance:status --tenants='.$tenant->id)->everyFiveMinutes();
            $schedule->command('tenants:run sync:messages --tenants='.$tenant->id)->everyMinute();
        }

        // $schedule->command('queue:work')->everyMinute()->withoutOverlapping();
        // $schedule->command('queue:restart')->hourly()->withoutOverlapping();
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
