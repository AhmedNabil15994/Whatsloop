<?php namespace App\Http\Middleware;

use App\Models\ApiAuth;
use App\Models\ApiKeys;
use App\Models\CentralUser;
use App\Models\Variable;
use Closure;
use Illuminate\Support\Facades\Session;

class ApiGeneralAuthEngine
{

    public function handle($request, Closure $next){
        if (!isset($_SERVER['HTTP_APIKEY'])) {
            return \TraitsFunc::ErrorMessage("API key is invalid", 401);
        }

        $apiKey = $_SERVER['HTTP_APIKEY'];

        $getAPIKey = ApiKeys::checkApiKey();
        if ($getAPIKey == null) {
            return \TraitsFunc::ErrorMessage("Invalid API Key, Please Check Kernel Authentication", 401);
        }

        define('API_KEY', $apiKey);

        return $next($request);
    }
}
