<?php

/*----------------------------------------------------------
Dashboard
----------------------------------------------------------*/
Route::group(['prefix' => '/'] , function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardControllers::class,'Dashboard'])->name('userDash');

    Route::get('/checkout', [App\Http\Controllers\DashboardControllers::class,'checkout'])->name('checkout');
    Route::post('/checkout', [App\Http\Controllers\DashboardControllers::class,'postCheckout']);
    Route::post('/completeOrder', [App\Http\Controllers\DashboardControllers::class,'completeOrder']);
    
    Route::post('/changeChannel', [App\Http\Controllers\DashboardControllers::class,'changeChannel']);
    
    Route::post('/changeTheme', [App\Http\Controllers\DashboardControllers::class,'changeTheme']);
    Route::post('/changeTheme/default', [App\Http\Controllers\DashboardControllers::class,'changeThemeToDefault']);

    Route::get('/faq', [App\Http\Controllers\DashboardControllers::class,'faqs']);
    Route::get('/helpCenter', [App\Http\Controllers\DashboardControllers::class,'helpCenter']);
    Route::post('/helpCenter/addRate', [App\Http\Controllers\DashboardControllers::class,'addRate']);

    Route::get('/QR', [App\Http\Controllers\DashboardControllers::class,'qrIndex']);
    Route::post('/QR/updateName', [App\Http\Controllers\DashboardControllers::class,'updateName']);
    Route::post('/QR/getQR', [App\Http\Controllers\DashboardControllers::class,'getQR']);
    Route::get('/QR/finish/{modID}', [App\Http\Controllers\DashboardControllers::class,'finishModID']);
    Route::post('/QR/editTemplate', [App\Http\Controllers\DashboardControllers::class,'editTemplate']);


});