<?php namespace App\Http\Middleware;

use Closure;
use Session;
use Config;
use App;

class LanguageMiddleware {
    public function handle($request, Closure $next){
    	$lang = Session::has('locale') ? Session::get('locale') : Config::get('app.locale');
        App::setLocale($lang);
        define('LANGUAGE_PREF', $lang);
        define('DIRECTION', $lang == 'ar' ? 'rtl' : 'ltr');
        define('DIR1', $lang == 'ar' ? 'right' : 'left');
        define('DIR2', $lang == 'ar' ? 'left' : 'right');
        return $next($request);
    }
}
