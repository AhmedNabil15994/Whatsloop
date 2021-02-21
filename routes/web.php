<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');


    
});

Route::post('tenant',[App\Http\Controllers\TenantsController::class, 'store'])->name('tenants.save');

Route::group(['prefix' => 'manager/auth' , 'namespace' => '\App\Http\Controllers\Manager\Auth','as' => 'manager.'],function(){

    //login
    Route::get('/login','LoginController@showLoginForm')->name('show.login');
    Route::post('/login','LoginController@login')->name('login');
    Route::post('/logout','LoginController@logout')->name('logout');

    //Forgot Password Routes
    Route::get('/password/reset','ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('/password/email','ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    //Reset Password Routes
    Route::get('/password/reset/{token}','ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('/password/reset','ResetPasswordController@reset')->name('password.update');

});


Route::group(['middleware' => 'auth:managers','prefix' => 'dashboard','namespace' => '\App\Http\Controllers\Manager','as' => 'manager.'],function(){
    Route::get('/','DashboardController@index')->name('dashboard');

});
