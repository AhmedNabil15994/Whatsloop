<?php namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class RoutesGate {

    /*----------------------------------------------*/
    /*               Route Permissions              */
    /*----------------------------------------------*/

    public function handle($request, Closure $next) {

        $route = \Route::getRoutes()->match($request);

        $route = $route->getActionName();
        $route = str_replace('\\','/',$route);
        $route = explode('/',$route);
        $currentController = end($route);
        
        $controllers = \Helper::getPermissions();
        $rules = isset($controllers[$currentController]) ? (array) $controllers[$currentController] : [];
        $availableRules = [
            'general',
            'auth',
        ];

        if(count(array_intersect($availableRules, $rules)) > 0) {
            return $next($request);
        }

        $checkPermissions = User::userPermission($rules);
        if(!$checkPermissions) {
            return redirect('401');
        }

        return $next($request);
    }

}
