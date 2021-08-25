<?php namespace App\Http\Middleware;

use App\Models\Channel;
use Closure;
use Illuminate\Support\Facades\Session;

class GeneralAuthEngine
{

    public function handle($request, Closure $next){

        if($request->segment(1) == 'uploads' || in_array($request->segment(2) , ['uploads','testResult','success']) ){
            return $next($request);
        }


        if($request->segment(1) == 'paytabs'){
            $profileId = @$_SERVER['HTTP_PROFILEID'];
            $serverKey = @$_SERVER['HTTP_SERVERKEY'];

            if ($profileId == null || $serverKey == null) {
                return \TraitsFunc::ErrorMessage("Paytabs Authentication failed.", 401);
            }

            define('PROFILE_ID', $profileId);
            define('SERVER_KEY', $serverKey);
        }elseif($request->segment(1) == 'noon'){
            $businessId = @$_SERVER['HTTP_BUSINESSID'];
            $appName = @$_SERVER['HTTP_APPNAME'];
            $appKey = @$_SERVER['HTTP_APPKEY'];
            $authKey = @$_SERVER['HTTP_AUTHKEY'];

            if ($businessId == null || $appName == null || $appKey == null || $authKey == null) {
                return \TraitsFunc::ErrorMessage("Noon Authentication failed.", 401);
            }

            define('BUSINESS_ID', $businessId);
            define('APP_NAME', $appName);
            define('APP_KEY', $appKey);
            define('AUTH_KEY', $authKey);
        }

        return $next($request);
    }
}
