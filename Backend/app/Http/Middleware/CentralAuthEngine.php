<?php namespace App\Http\Middleware;

use App\Models\ApiAuth;
use App\Models\CentralUser;
use Closure;
use Illuminate\Support\Facades\Session;

class CentralAuthEngine
{

    public function handle($request, Closure $next){
        define('USER_ID', Session::get('user_id'));
        if ($request->segment(1) == null && !(USER_ID && USER_ID != '')) {
            session()->flush();

            \Session::flash('error', trans('auth.mustLogin'));
            return Redirect('/login');
        }

        if (in_array($request->segment(1), ['login','livechatApi','changeLang','impersonate','getResetPassword','changePassword','completeReset'])) {
            return $next($request);
        }

        if (!(USER_ID && USER_ID != '')) {
            session()->flush();

            \Session::flash('error', trans('auth.mustLogin'));
            return Redirect('/login');
        }

        define('GROUP_ID', Session::get('group_id'));
        define('GROUP_NAME', Session::get('group_name'));
        define('FULL_NAME', Session::get('name'));

        // Update login date realtime
        $userObj = CentralUser::getOne(USER_ID);
        define('GLOBAL_ID', $userObj->global_id);
        $permissions = CentralUser::checkUserPermissions($userObj);        
        define('IS_ADMIN', $userObj->group_id == 1 ? true : false);
        define('PERMISSIONS', $permissions);

        return $next($request);
    }
}
