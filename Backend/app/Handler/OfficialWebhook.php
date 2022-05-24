<?php
namespace App\Handler;
use \Spatie\WebhookClient\ProcessWebhookJob;

use Http;
use Session;
use Throwable;

class OfficialWebhook extends ProcessWebhookJob{
	public function handle(){
	    $data = json_decode($this->webhookCall, true);
	    $allData = $data['payload'];
		Logger($allData);
	}
    
}