<?php

/*----------------------------------------------------------
Dashboard
----------------------------------------------------------*/
Route::group(['prefix' => '/'] , function () {
    Route::get('/dashboard', [App\Http\Controllers\CentralDashboardControllers::class,'Dashboard'])->name('userDash');
    Route::post('/changeChannel', [App\Http\Controllers\CentralDashboardControllers::class,'changeChannel']);
    Route::post('/changeTheme', [App\Http\Controllers\CentralDashboardControllers::class,'changeTheme']);
    Route::post('/changeTheme/default', [App\Http\Controllers\CentralDashboardControllers::class,'changeThemeToDefault']);
});