<?php

/*----------------------------------------------------------
Dashboard
----------------------------------------------------------*/
Route::group(['prefix' => '/'] , function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardControllers::class,'Dashboard']);
    Route::post('/changeChannel', [App\Http\Controllers\DashboardControllers::class,'changeChannel']);
    Route::post('/changeTheme', [App\Http\Controllers\DashboardControllers::class,'changeTheme']);
    Route::post('/changeTheme/default', [App\Http\Controllers\DashboardControllers::class,'changeThemeToDefault']);
});