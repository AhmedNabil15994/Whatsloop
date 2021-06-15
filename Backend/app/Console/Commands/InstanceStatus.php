<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserStatus;
use App\Models\User;

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

        $statusInt = 4;

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

        $oldStatusObj = UserStatus::orderBy('id','DESC')->take(2)->get();
        $check = 0;
        if($oldStatusObj){
            if($oldStatusObj[0]->status == 4 && $oldStatusObj[1]->status == 4){
                $check = 1;
            }
            
            if($statusInt == 4 && $check  == 0){
                $channelObj = \DB::connection('main')->table('channels')->first();
                $whatsLoopObj =  new \MainWhatsLoop($channelObj->id,$channelObj->token);
                $data['phone'] = str_replace('+','',User::first()->emergency_number);
                $data['body'] = 'Connection Closed and you got a new QR Code , please go and scan it!';
                $test = $whatsLoopObj->sendMessage($data);
                if(!isset($result['status']) || $result['status']['status'] != 1){
                    $userStatusObj = new UserStatus;
                    $userStatusObj->status = $statusInt;
                    $userStatusObj->created_at = date('Y-m-d H:i:s');
                    $userStatusObj->save();
                }
            }
        }
    }
}
