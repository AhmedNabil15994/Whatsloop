<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\CentralChannel;

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
        \App\Console\Commands\SyncLabels::class,
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

        $channels = CentralChannel::dataList()['data'];
        foreach($channels as $channel){
            if($channel->leftDays > 0 && $channel->tenant_id != null){
                $schedule->command('tenants:run groupMsg:send --tenants='.$channel->tenant_id)->everyMinute();
                $schedule->command('tenants:run instance:status --tenants='.$channel->tenant_id)->everyMinute();
                $schedule->command('tenants:run sync:labels --tenants='.$channel->tenant_id)->everyMinute();
                $schedule->command('tenants:run sync:messages --tenants='.$channel->tenant_id)->withoutOverlapping()->everyMinute();
                $schedule->command('tenants:run sync:dialogs --tenants='.$channel->tenant_id)->withoutOverlapping()->everyMinute();
                $schedule->command('tenants:run check:contacts --tenants='.$channel->tenant_id)->everyMinute()->withoutOverlapping();
            }
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
