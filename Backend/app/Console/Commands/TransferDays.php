<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CentralUser;
use App\Models\UserAddon;
use App\Models\CentralChannel;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Variable;

class TransferDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer 3 Days for every Channel';

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

        $channelObj = CentralChannel::first();
        $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
        $result  = $mainWhatsLoopObj->channels();
        $allChannels = [];
        if(isset($result['data']) && isset($result['data']['channels'])){
            $allChannels = $result['data']['channels'];
        }

        $mainChannel = $allChannels[0];
        $later = new \DateTime(date('Y-m-d',$mainChannel['paidTill'] / 1000));
        $earlier = new \DateTime(date('Y-m-d'));
        $balanceDays = abs($later->diff($earlier)->format("%a"));
        $channels = CentralChannel::dataList()['data'];

        // Normal Script
        if($balanceDays < ( (3 * count($channels) )  + 2 ) ){
            $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
            $data['body'] = "You Have To recharge at least ". (  ( (3 * count($channels) )  + 2 ) - $balanceDays )  ." day To Complete Transfering Days for all channels";
            $data['phone'] = str_replace('+','',CentralUser::first()->phone);
            $mainWhatsLoopObj->sendMessage($data);
            return 1;
        }

        $activeChannels = [];
        foreach ($channels as $key => $value) {
            if($key > 0 && $value->leftDays > 0){
                $activeChannels[] = $value->id; 
            }
        }

        foreach($allChannels as $channel){
            $later = new \DateTime(date('Y-m-d',$mainChannel['paidTill'] / 1000));
            $earlier = new \DateTime(date('Y-m-d'));
            $duration = abs($later->diff($earlier)->format("%a"));
            if(in_array($channel['id'],$activeChannels) && $duration <= 2){
                $transferDaysData = [
                    'receiver' => $channel['id'],
                    'days' => 3,
                    'source' => $channelObj->id,
                ];

                $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
                $result = $updateResult->json();
            }
        }

    }
}
