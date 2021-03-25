<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncMessagesJob;

class SyncMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync User Messages Every Minute';

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
        $updateResult = $mainWhatsLoopObj->messages($data);
        if(isset($updateResult['data']) && !empty($updateResult['data'])){
            $result = $updateResult->json();
            dispatch(new SyncMessagesJob($result['data']['messages']));
        }
        
    }
}
