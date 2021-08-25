<?php namespace App\Http\Middleware;

use Closure;
use Config;
use App;
use Illuminate\Support\Facades\Session;

class ApiLanguageMiddleware {

    public function handle($request, Closure $next){
        if(!empty($_SERVER['HTTP_LANG']) && isset($_SERVER['HTTP_LANG'])){
            App::setLocale($_SERVER['HTTP_LANG']);
            define('LANGUAGE_PREF',$_SERVER['HTTP_LANG']);
        }else{
            App::setLocale('en');
            define('LANGUAGE_PREF','en');
        }
        return $next($request);
    }
}
