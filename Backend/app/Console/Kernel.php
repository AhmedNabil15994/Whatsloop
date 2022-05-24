<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\CentralChannel;
use App\Models\UserAddon;

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
        \App\Console\Commands\SetAddonReports::class,
        \App\Console\Commands\SyncWhmcs::class,
        \App\Console\Commands\SendScheduleCarts::class,
        \App\Console\Commands\SyncZidAbandonedCarts::class,

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
                if(UserAddon::where('addon_id',4)->where('tenant_id',$channel->tenant_id)->where('status',1)->first()){
                    $schedule->command('tenants:run sync:zid --tenants='.$channel->tenant_id)->cron('*/30 * * * *')->withoutOverlapping();
                }
                $schedule->command('tenants:run groupMsg:send --tenants='.$channel->tenant_id)->everyMinute();
                $schedule->command('tenants:run instance:status --tenants='.$channel->tenant_id)->everyMinute();
                $schedule->command('tenants:run sync:labels --tenants='.$channel->tenant_id)->everyMinute();
                $schedule->command('tenants:run send:scheduleCarts --tenants='.$channel->tenant_id)->hourly();
                $schedule->command('tenants:run sync:messages --tenants='.$channel->tenant_id)->withoutOverlapping()->everyMinute();
                $schedule->command('tenants:run sync:dialogs --tenants='.$channel->tenant_id)->withoutOverlapping()->everyMinute();
                $schedule->command('tenants:run check:contacts --tenants='.$channel->tenant_id)->everyMinute()->withoutOverlapping();
            }
        }

        // $schedule->command('set:addonReports',[1])->everyMinute();
        // $schedule->command('set:addonReports',[2])->everyMinute();
        // $schedule->command('set:invoices')->cron('0 9,12 * * *');
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
