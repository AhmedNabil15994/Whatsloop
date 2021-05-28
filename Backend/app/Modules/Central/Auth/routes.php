<?php

/*----------------------------------------------------------
User Auth
----------------------------------------------------------*/

Route::group(['prefix' => '/'] , function () {
	$authController = App\Http\Controllers\CentralAuthControllers::class;


    Route::get('/register',function(){
        return view('central.welcome');    
    });
    Route::post('register',[App\Http\Controllers\CentralController::class, 'register'])->name('central.register');

    Route::post('login',[App\Http\Controllers\CentralController::class, 'login'])->name('central.login');

    Route::get('login',[App\Http\Controllers\CentralController::class, 'showLogin'])->name('central.show.login');
    Route::post('redirection',[App\Http\Controllers\CentralController::class, 'redirectLogin'])->name('central.redirection');
    
    



    
    Route::get('/', function(){dd('landingPage');});

    Route::get('/login', [$authController,'login'])->name('login');
    Route::post('/login', [$authController,'doLogin'])->name('doLogin');
    Route::post('/checkByCode', [$authController,'checkByCode']);
    Route::get('/logout', [$authController,'logout']);

    Route::get('/getResetPassword', [$authController,'getResetPassword'])->name('getResetPassword');
    Route::post('/resetPassword', [$authController,'resetPassword'])->name('resetPassword');
    Route::get('/changePassword', [$authController,'changePassword']);
    Route::post('/checkResetPassword', [$authController,'checkResetPassword']);
    Route::post('/completeReset', [$authController,'completeReset']);

	Route::post('/changeLang', [$authController,'changeLang'])->name('changeLang');

    // Route::get('impersonate/{token}',[App\Http\Controllers\ImpersonatesController::class, 'index'])->name('impersonate');
});