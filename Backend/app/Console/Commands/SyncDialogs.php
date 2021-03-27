<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncDialogsJob;

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

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['limit'] = 0;
        $updateResult = $mainWhatsLoopObj->dialogs($data);
        if(isset($updateResult['data']) && !empty($updateResult['data'])){
            $result = $updateResult->json();
            dispatch(new SyncDialogsJob($result['data']['dialogs']));
        }
        
    }
}
