<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class TransferDays implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;
    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $mainChannelId;
    public $mainChannelToken;
    public $userChannelId;
    public $days;
    
    public function __construct($mainChannelId,$mainChannelToken,$userChannelId,$days)
    {
        $this->mainChannelId = $mainChannelId;
        $this->mainChannelToken = $mainChannelToken;
        $this->userChannelId = $userChannelId;
        $this->days = $days;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mainWhatsLoopObj = new \MainWhatsLoop($this->mainChannelId,$this->mainChannelToken);
        $transferDaysData = [
            'receiver' => $this->userChannelId,
            'days' => $this->days,
            'source' => $this->mainChannelId,
        ];

        $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
        $result = $updateResult->json();
    }

}
