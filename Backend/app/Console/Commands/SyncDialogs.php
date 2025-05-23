<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncDialogsJob;
use App\Models\ChatDialog;
use App\Models\User;

class SyncDialogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:dialogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync User Dialogs Every Minute';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        if(User::first()->setting_pushed == 0){
            sleep(120);
        }
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['limit'] = 0;
        $updateResult = $mainWhatsLoopObj->dialogs($data);
        $updateResult = $updateResult->json();
        if(isset($updateResult['data']) && !empty($updateResult['data'])){
            $count = count($updateResult['data']['dialogs']);
            if($count > ChatDialog::count()){
                try {
                    dispatch(new SyncDialogsJob($updateResult['data']['dialogs']))->onConnection('cjobs');
                } catch (Exception $e) {
                    
                }
            }
        }
        
    }
}
