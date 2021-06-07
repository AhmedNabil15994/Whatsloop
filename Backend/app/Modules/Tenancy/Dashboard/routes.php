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
});