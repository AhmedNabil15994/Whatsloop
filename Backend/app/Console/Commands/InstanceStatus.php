<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserStatus;

class InstanceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instance:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Instance Status Every 5 Minutes';

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
        $result = $mainWhatsLoopObj->status();
        $result = $result->json();
        if(isset($result['data']) && !empty($result['data'])){
            $status = $result['data']['accountStatus'];
            if($status == 'authenticated'){
                $statusInt = 1;
            }else if($status == 'init'){
                $statusInt = 2;
            }else if($status == 'loading'){
                $statusInt = 3;
            }else if($status == 'got qr code'){
                $statusInt = 4;
            }
        }


        if(isset($result['status']) && !empty($result['status'])){
            if($result['status']['status'] == 1){
                $userStatusObj = new UserStatus;
                $userStatusObj->status = $statusInt;
                $userStatusObj->created_at = date('Y-m-d H:i:s');
                $userStatusObj->save();
            }
        }
        
    }
}
