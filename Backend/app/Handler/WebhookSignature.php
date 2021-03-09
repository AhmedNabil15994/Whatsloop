<?php

namespace App\Handler;
use Illuminate\Http\Request;
use Spatie\WebhookClient\Exceptions\WebhookFailed;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;

class WebhookSignature implements SignatureValidator{
    public function isValid(Request $request, WebhookConfig $config): bool{
		return true;
   	}
}