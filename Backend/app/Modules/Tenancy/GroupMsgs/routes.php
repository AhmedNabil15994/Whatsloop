<?php 

/*----------------------------------------------------------
Group Messages
----------------------------------------------------------*/
Route::group(['prefix' => '/groupMsgs'] , function () {
    $controller = \App\Http\Controllers\GroupMsgsControllers::class;
    Route::get('/', [$controller,'index']);
    Route::get('/add', [$controller,'add']);
    Route::get('/charts', [$controller,'charts']);
	Route::post('/create', [$controller,'create']);
    Route::get('/view/{id}', [$controller,'view']);
    Route::get('/resend/{id}', [$controller,'resend']);

    /*----------------------------------------------------------
    Images
    ----------------------------------------------------------*/

    Route::post('/add/uploadImage/{type}', [$controller,'uploadImage']);
});