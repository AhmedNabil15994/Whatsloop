<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
Use App\Models\ContactReport;

class FixReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $id;
    
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $testData = ContactReport::where('status',0)->where('group_message_id',$this->id)->get();
        foreach($testData as $oneRow){
            $webhookObj = \DB::table('webhook_calls')->where('payload','LIKE','%'.$oneRow->message_id.'%')->orderBy('created_at','DESC')->first();
            if($webhookObj){
                $payLoad = json_decode($webhookObj->payload);
                $status = 0;
                if(isset($payLoad->ack) && !empty($payLoad->ack)){
                    $ackStatus = $payLoad->ack[0]->status;
                    if($ackStatus == 'sent'){
                        $status = 1;
                    }else if($ackStatus == 'delivered'){
                        $status = 2;
                    }else if($ackStatus == 'viewed'){
                        $status = 3;
                    }
                    ContactReport::where('message_id',$payLoad->ack[0]->id)->update(['status'=>$status]);
                }
            }
        }
    }

    
}
