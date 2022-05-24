<?php

namespace App\Handler;
use Illuminate\Http\Request;
use Spatie\WebhookClient\Exceptions\WebhookFailed;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;

class OfficialWebhookSignature implements SignatureValidator{
    public function isValid(Request $request, WebhookConfig $config): bool{
        $hub_verify_token = $config->signingSecret;
        $comingToken = $request->get('hub_verify_token');
        if($hub_verify_token == $comingToken){
            return true;
        }
        return false;
   	}
}