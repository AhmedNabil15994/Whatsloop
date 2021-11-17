<?php namespace App\Http\Middleware;

use App\Models\Channel;
use Closure;
use Illuminate\Support\Facades\Session;

class GeneralAuthEngine
{

    public function handle($request, Closure $next){

        if($request->segment(1) == 'uploads' || $request->segment(2) == 'uploads' ){
            return $next($request);
        }

        $channelsRoutes = ['createChannel','deleteChannel','transferDays',null];
        if (!isset($_SERVER['HTTP_CHANNELID'])  && $request->segment(1) != 'channels' && !in_array($request->segment(2), $channelsRoutes)) {
            return \TraitsFunc::ErrorMessage("Channel ID is invalid", 401);
        }

        if (!isset($_SERVER['HTTP_CHANNELTOKEN']) && $request->segment(1) != 'channels' && !in_array($request->segment(2), $channelsRoutes)) {
            return \TraitsFunc::ErrorMessage("Channel Token is invalid", 401);
        }

        if($request->segment(1) != 'channels' && !in_array($request->segment(2), $channelsRoutes)){
            $channelId = $_SERVER['HTTP_CHANNELID'];
            $channelToken = $_SERVER['HTTP_CHANNELTOKEN'];

            $checkChannel = Channel::getUserChannel($channelId,$channelToken);
            if ($checkChannel == null) {
                return \TraitsFunc::ErrorMessage("Invalid Channel, Please Check Your Credentials", 401);
            }

            // if(($request->segment(1) == 'products') && in_array($request->segment(2),['getProducts','getProduct','sendProduct'])){
            //     $checkChannel = Channel::NotDeleted()->first();
            // }
           

            define('CHANNEL_ID', $checkChannel->id);
            define('CHANNEL_TOKEN', $checkChannel->token);
        }

        return $next($request);
    }
}
