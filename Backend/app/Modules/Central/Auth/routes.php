<?php

/*----------------------------------------------------------
User Auth
----------------------------------------------------------*/

Route::group(['prefix' => '/'] , function () {
    $authController = App\Http\Controllers\CentralAuthControllers::class;


    // Route::get('/register',function(){
    //     return view('central.welcome');    
    // });
    // Route::post('register',[App\Http\Controllers\CentralController::class, 'register'])->name('central.register');

    // Route::post('login',[App\Http\Controllers\CentralController::class, 'login'])->name('central.login');

    // Route::get('login',[App\Http\Controllers\CentralController::class, 'showLogin'])->name('central.show.login');
    Route::post('redirection',[App\Http\Controllers\CentralController::class, 'redirectLogin'])->name('central.redirection');
    
    



    
    Route::get('/', function(){ return redirect()->to('/login'); });

    Route::get('/login', [$authController,'login']);
    Route::get('/appLogin', [$authController,'appLogin']);
    Route::post('/login', [$authController,'doLogin']);
    Route::post('/checkByCode', [$authController,'checkByCode']);
    Route::get('/logout', [$authController,'logout']);

    Route::get('/getResetPassword', [$authController,'getResetPassword']);
    Route::post('/resetPassword', [$authController,'resetPassword']);
    Route::get('/changePassword', [$authController,'changePassword']);
    Route::post('/checkResetPassword', [$authController,'checkResetPassword']);
    Route::post('/completeReset', [$authController,'completeReset']);

    Route::get('/checkAvailability', [$authController,'checkAvailability'])->name('checkAvailability');
    Route::post('/checkAvailability', [$authController,'postCheckAvailability'])->name('postCheckAvailability');
    Route::post('/checkAvailabilityCode', [$authController,'checkAvailabilityCode'])->name('checkAvailabilityCode');
    
    Route::get('/register', [$authController,'register'])->name('register');
    Route::post('/register', [$authController,'postRegister']);

    Route::post('/changeLang', [$authController,'changeLang']);

    // Route::get('impersonate/{token}',[App\Http\Controllers\ImpersonatesController::class, 'index'])->name('impersonate');
});