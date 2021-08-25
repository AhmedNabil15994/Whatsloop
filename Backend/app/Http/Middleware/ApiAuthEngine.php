<?php namespace App\Http\Middleware;

use App\Models\ApiAuth;
use App\Models\ApiKeys;
use App\Models\CentralUser;
use App\Models\Domain;
use Closure;
use Illuminate\Support\Facades\Session;

class ApiAuthEngine
{

    public function handle($request, Closure $next){

        if (!isset($_SERVER['HTTP_APIKEY'])) {
            return \TraitsFunc::ErrorMessage("API key is invalid", 401);
        }

        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return \TraitsFunc::ErrorMessage("unauthorized", 401);
        }

        $apiAuthToken = $_SERVER['HTTP_AUTHORIZATION'];

        if ($request->segment(1) == null && $apiAuthToken != '') {
            return \TraitsFunc::ErrorMessage("unauthorized", 401);
        }

        if (in_array($request->segment(1), ['login', 'register'])) {
            return \TraitsFunc::ErrorMessage("unauthorized", 401);
        }

        //Check token
        $checkAuth = ApiAuth::checkUserToken($apiAuthToken);
        if($checkAuth == null){
            \Auth::logout();
            session()->flush();
            return \TraitsFunc::ErrorMessage("Session Expired, Please Login Again!", 401);
        }


        $centralObj = CentralUser::getData(CentralUser::getOne(USER_ID));
        $domainObj = Domain::where('domain',$centralObj->domain)->first();
        $tenantUserObj = \DB::connection('main')->table('tenant_users')->where('tenant_id',$domainObj->tenant_id)->first();

        define('GLOBAL_ID', $tenantUserObj->global_user_id);
        define('TENANT_ID', $domainObj->tenant_id);
        define('APP_TOKEN', $apiAuthToken);
        define('IS_ADMIN', $centralObj->group_id == 0 ? 1 : 0);
        define('DOMAIN', $centralObj->domain);
        return $next($request);
    }
}
