<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Middleware\ScopeSessions;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Route::middleware([
//     'general',
//     InitializeTenancyBySubdomain::class,
//     PreventAccessFromCentralDomains::class,
//     ScopeSessions::class
// ])->group(function () {

    
//     Route::get('impersonate/{token}',[App\Http\Controllers\ImpersonatesController::class, 'index'])->name('impersonate');
    
//     Route::get('/',function(){
//         $routeLogin = route('login');
//         return view('tenant.welcome',compact('routeLogin'));
//     })->name('welcome');


//     Route::group(['prefix' => 'dashboard','middleware' => 'auth:web','namespace' => '\App\Http\Controllers\Tenant','as' => 'tenant.'],function(){
//         //dd(auth()->id());

//         //Dashboard routes
//         Route::get('/','DashboardController@index')->name('dashboard');

//     });

//     Route::get('/login', function(){
//         return redirect()->route('welcome');
//     })->name('login');
//     Route::post('/login',[App\Http\Controllers\Auth\LoginController::class, 'login']);
//     Route::post('/logout',[App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
//     //Auth::routes();
//     //Route::get('/home', [App\Http\Controllers\Auth\HomeController::class, 'index'])->name('home');


// });
