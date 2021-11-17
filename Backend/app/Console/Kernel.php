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
        \App\Console\Commands\SyncDialogs::class,
        \App\Console\Commands\SetInvoices::class,
        \App\Console\Commands\PushChannelSetting::class,
        \App\Console\Commands\PushAddonSetting::class,
        \App\Console\Commands\TransferDays::class,
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
            $schedule->command('tenants:run instance:status --tenants='.$tenant->id)->everyMinute();
            $schedule->command('tenants:run sync:messages --tenants='.$tenant->id)->withoutOverlapping()->everyMinute();
            $schedule->command('tenants:run sync:dialogs --tenants='.$tenant->id)->withoutOverlapping()->everyMinute();
        }
        $schedule->command('set:invoices')->cron('0 9,12 * * *');
        // $schedule->command('push:channelSetting')->everyMinute();
        // $schedule->command('push:addonSetting')->daily();
        // $schedule->command('transfer:days')->cron('0 0 */3 * *');
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
