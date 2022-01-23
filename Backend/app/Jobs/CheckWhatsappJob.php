<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
Use App\Models\Contact;

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
        foreach ($this->contacts as $contactArr) {
            try {
                $contact = (object) $contactArr;
                $contactObj = Contact::where('group_id',$contact->group_id)->where('phone',$contact->phone)->first();
                if($contactObj){
                    $contactObj->update($contactArr);
                }else{
                    $contactObj = Contact::create($contactArr);
                }

                if($contact->phone != null){   
                    $result = $this->checkWhatsappAvailability(str_replace('+', '', $contact->phone));
                    $contactObj->has_whatsapp = $result;
                    $contactObj->save();
                }
            } catch (Exception $e) {
                // Logger('')   
            }
        }
        sleep(120);
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
