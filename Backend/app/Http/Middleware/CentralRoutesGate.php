<?php namespace App\Http\Middleware;

use App\Models\CentralUser;
use Closure;

class CentralRoutesGate {

    /*----------------------------------------------*/
    /*               Route Permissions              */
    /*----------------------------------------------*/

    public function handle($request, Closure $next) {

        $controllers = config('central_permissions');
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
 
        if(count(array_intersect($availableRules, $rules)) > 0) {
            return $next($request);
        }

        $checkPermissions = CentralUser::userPermission($rules);
        if(!$checkPermissions) {
            return redirect('401');
        }

        return $next($request);
    }

}
