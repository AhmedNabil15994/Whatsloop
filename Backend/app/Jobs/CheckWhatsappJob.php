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
    use Dispatchable, InteractsWithQueue, Queueable;

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
            try {
                if($contact->phone != null){
                    $result = $this->checkWhatsappAvailability(str_replace('+', '', $contact->phone));
                    $contact->has_whatsapp = $result;
                    $contact->save();
                }
            } catch (Exception $e) {
                // Logger('')   
            }
        }
        return 1;
    }

    public function checkWhatsappAvailability($contact){
        $checkData['phone'] = $contact;
        $mainWhatsLoopObj = new \MainWhatsLoop();

        $checkResult = $mainWhatsLoopObj->checkPhone($checkData);
        $result = $checkResult->json();

        if($result['status']['status'] != 1){
            $status = 0;
        }

        if(isset($result['data'])){
            $status = $result['data']['result'] == 'exists' ? 1 : 0;
        }

        return $status;
    }
}
