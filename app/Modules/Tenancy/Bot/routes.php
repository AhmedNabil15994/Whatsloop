<?php 

/*----------------------------------------------------------
Groups
----------------------------------------------------------*/
Route::group(['prefix' => '/bots'] , function () {
    $controller = \App\Http\Controllers\BotControllers::class;
    Route::get('/', [$controller,'index']);
    Route::get('/add', [$controller,'add']);
    Route::get('/arrange', [$controller,'arrange']);
    Route::get('/charts', [$controller,'charts']);
    Route::get('/edit/{id}', [$controller,'edit']);
    Route::get('/copy/{id}', [$controller,'copy']);
    Route::post('/update/{id}', [$controller,'update']);
    Route::post('/fastEdit', [$controller,'fastEdit']);
	Route::post('/create', [$controller,'create']);
    Route::get('/delete/{id}', [$controller,'delete']);
    Route::post('/arrange/sort', [$controller,'sort']);

    /*----------------------------------------------------------
    Images
    ----------------------------------------------------------*/

    Route::post('/add/uploadImage/{type}', [$controller,'uploadImage']);
    Route::post('/edit/editImage/{type}', [$controller,'uploadImage']);
    Route::post('/edit/{id}/deleteImage', [$controller,'deleteImage']);
});