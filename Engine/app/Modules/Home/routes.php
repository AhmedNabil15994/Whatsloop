<?php

/*----------------------------------------------------------
Instances
----------------------------------------------------------*/
Route::group(['prefix' => '/instances'] , function () {
	$controller = App\Http\Controllers\HomeControllers::class;
    Route::post('/{status}', [$controller,'index'])->middleware('instance');
});


/*----------------------------------------------------------
Messages
----------------------------------------------------------*/
Route::group(['prefix' => '/messages'] , function () {
	$controller = App\Http\Controllers\HomeControllers::class;
    Route::post('/{status}', [$controller,'index'])->middleware('instance');
});


/*----------------------------------------------------------
Dialogs
----------------------------------------------------------*/
Route::group(['prefix' => '/dialogs'] , function () {
	$controller = App\Http\Controllers\HomeControllers::class;
    Route::post('/{status}', [$controller,'index'])->middleware('instance');
});


/*----------------------------------------------------------
Webhooks
----------------------------------------------------------*/
Route::group(['prefix' => '/webhooks'] , function () {
	$controller = App\Http\Controllers\HomeControllers::class;
    Route::post('/{status}', [$controller,'index'])->middleware('instance');
});


/*----------------------------------------------------------
Queues
----------------------------------------------------------*/
Route::group(['prefix' => '/queues'] , function () {
	$controller = App\Http\Controllers\HomeControllers::class;
    Route::post('/{status}', [$controller,'index'])->middleware('instance');
});


/*----------------------------------------------------------
Ban Settings
----------------------------------------------------------*/
Route::group(['prefix' => '/ban'] , function () {
	$controller = App\Http\Controllers\HomeControllers::class;
    Route::post('/{status}', [$controller,'index'])->middleware('instance');
});


/*----------------------------------------------------------
Testing
----------------------------------------------------------*/
Route::group(['prefix' => '/testing'] , function () {
	$controller = App\Http\Controllers\HomeControllers::class;
    Route::post('/{status}', [$controller,'index'])->middleware('instance');
});


/*----------------------------------------------------------
Users
----------------------------------------------------------*/
Route::group(['prefix' => '/users'] , function () {
	$controller = App\Http\Controllers\HomeControllers::class;
    Route::post('/{status}', [$controller,'index'])->middleware('instance');
});

/*----------------------------------------------------------
Channels
----------------------------------------------------------*/
Route::group(['prefix' => '/channels'] , function () {
	$controller = App\Http\Controllers\HomeControllers::class;
    Route::post('/createChannel', [$controller,'createChannel']);
    Route::post('/deleteChannel', [$controller,'deleteChannel']);
    Route::post('/transferDays', [$controller,'transferDays']);
    Route::post('/', [$controller,'channels']);
});