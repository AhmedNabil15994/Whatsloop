<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $contacts;
    public $messageObj;
    
    public function __construct($contacts)
    {
        $this->contacts = $contacts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        foreach ($this->contacts as $contact) {
            $result = $this->checkWhatsappAvailability(str_replace('+', '', $contact->phone));
            $contact->has_whatsapp = $result;
            $contact->save();
        }
    }

    public function checkWhatsappAvailability($contact){
        $checkData['chatId'] = $contact.'@c.us';
        $mainWhatsLoopObj = new \MainWhatsLoop();

        $checkResult = $mainWhatsLoopObj->userStatus($checkData);
        $result = $checkResult->json();

        $status = 1;
        if($result['status']['status'] != 1){
            $status = 0;
        }

        return $status;
    }
}
