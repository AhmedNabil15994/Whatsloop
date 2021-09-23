<?php

/*----------------------------------------------------------
PayTabs
----------------------------------------------------------*/
Route::group(['prefix' => '/paytabs'] , function () {
	$controller = App\Http\Controllers\PayTabsControllers::class;
    Route::post('/', [$controller,'index']);
    Route::any('/testResult', [$controller,'testResult']);
    Route::post('/success', [$controller,'success']);
});


/*----------------------------------------------------------
Noon
----------------------------------------------------------*/
Route::group(['prefix' => '/noon'] , function () {
    $controller = App\Http\Controllers\NoonControllers::class;
    Route::post('/', [$controller,'index']);
    Route::post('/subscription', [$controller,'newSubscription']);
    Route::post('/recurSubscription', [$controller,'recurSubscription']);
    Route::post('/subscription/mitUnsched', [$controller,'mitUnschedSubscription']);
    Route::post('/subscription/retrieve', [$controller,'retrieveSubscription']);
    Route::post('/subscription/cancel', [$controller,'cancelSubscription']);
    Route::any('/testResult', [$controller,'testResult']);
    Route::post('/success', [$controller,'success']);
});