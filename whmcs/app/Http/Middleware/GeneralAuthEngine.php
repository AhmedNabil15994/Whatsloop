<?php namespace App\Http\Middleware;

use App\Models\Channel;
use Closure;
use Illuminate\Support\Facades\Session;

class GeneralAuthEngine
{

    public function handle($request, Closure $next){
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return \TraitsFunc::ErrorMessage("Authorization Key is required", 401);
        }

        if (isset($_SERVER['HTTP_AUTHORIZATION']) && $_SERVER['HTTP_AUTHORIZATION'] != config('whmcs.api_access_key')) {
            return \TraitsFunc::ErrorMessage("Authorization Key is invalid", 401);
        }
        return $next($request);
    }
}
