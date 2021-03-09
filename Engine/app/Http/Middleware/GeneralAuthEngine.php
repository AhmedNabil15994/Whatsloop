<?php namespace App\Http\Middleware;

use App\Models\Channel;
use Closure;
use Illuminate\Support\Facades\Session;

class GeneralAuthEngine
{

    public function handle($request, Closure $next){
        if (!isset($_SERVER['HTTP_CHANNELID'])) {
            return \TraitsFunc::ErrorMessage("Channel ID is invalid", 401);
        }

        if (!isset($_SERVER['HTTP_CHANNELTOKEN'])) {
            return \TraitsFunc::ErrorMessage("Channel Token is invalid", 401);
        }

        $channelId = $_SERVER['HTTP_CHANNELID'];
        $channelToken = $_SERVER['HTTP_CHANNELTOKEN'];

        $checkChannel = Channel::getUserChannel($channelId,$channelToken);
        if ($checkChannel == null) {
            return \TraitsFunc::ErrorMessage("Invalid Channel, Please Check Your Credentials", 401);
        }

        define('CHANNEL_ID', $channelId);
        define('CHANNEL_TOKEN', $channelToken);

        return $next($request);
    }
}
