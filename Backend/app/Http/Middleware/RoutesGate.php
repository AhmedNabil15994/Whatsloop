<?php namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Session;

class RoutesGate {

    /*----------------------------------------------*/
    /*               Route Permissions              */
    /*----------------------------------------------*/

    public function handle($request, Closure $next) {
        $controllers = \Helper::getAllPerms();
        // dd($controllers);

        $route = \Route::getRoutes()->match($request);
        $route = $route->getActionName();
        $route = str_replace('\\','/',$route);
        $route = explode('/',$route);
        $currentController = end($route);
        $rules = isset($controllers[$currentController]) ? (array) $controllers[$currentController] : [];
        $availableRules = [
            'general',
            'auth',
        ];

        if(count(array_intersect($availableRules, $rules)) > 0 || \Request::segment(1) == 'livechatApi') {
            return $next($request);
        }

        if(\Request::segment(1) != 'livechat'){
            $checkPermissions = User::userPermission($rules);
            if(!$checkPermissions) {
                return redirect('401');
            }
        }

        return $next($request);
    }

}
