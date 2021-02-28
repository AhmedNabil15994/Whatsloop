<?php namespace App\Http\Middleware;

use App\Models\ApiAuth;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Session;

class AuthEngine
{

    public function handle($request, Closure $next){
        define('USER_ID', Session::get('user_id'));
        if ($request->segment(1) == null && !(USER_ID && USER_ID != '')) {
            \Auth::logout();
            session()->flush();

            \Session::flash('error', "يرجي تسجيل الدخول اولا!");
            return Redirect('/login');
        }

        if (in_array($request->segment(1), ['login','changeLang','impersonate','getResetPassword','changePassword','completeReset'])) {
            if (in_array($request->segment(1), ['login','changeLang','impersonate','getResetPassword','changePassword','completeReset'])) {
                return $next($request);
            } else {

            }
        }

        if (!(USER_ID && USER_ID != '')) {
            \Auth::logout();
            session()->flush();

            \Session::flash('error', "يرجي تسجيل الدخول اولا!");
            return Redirect('/login');
        }

        define('GROUP_ID', Session::get('group_id'));
        define('GROUP_NAME', Session::get('group_name'));
        define('FULL_NAME', Session::get('name'));

        // Update login date realtime
        $userObj = User::getOne(USER_ID);
        if ($userObj != null) {
            
        }

        $permissions = User::checkUserPermissions($userObj);        
        define('IS_ADMIN', $userObj->group_id == 1 ? true : false);
        define('PERMISSIONS', $permissions);

        return $next($request);
    }
}
