<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReadChatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $messages;
    public $status;
    public function __construct($messages,$status)
    {
        $this->messages = $messages;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->messages as $message) {
            $status = $this->status;
            $mainWhatsLoopObj = new \MainWhatsLoop();
            $data['chatId'] = str_replace('@c.us', '', $message);
            if($status == 1){
                $updateResult = $mainWhatsLoopObj->readChat($data);
            }else{
                $updateResult = $mainWhatsLoopObj->unreadChat($data);
            }

            $result = $updateResult->json();
        }
    }
}
