<?php 

/*----------------------------------------------------------
Clients
----------------------------------------------------------*/
Route::group(['prefix' => '/clients'] , function () {
    $controller = \App\Http\Controllers\ClientControllers::class;
    Route::get('/', [$controller,'index']);
    Route::get('/add', [$controller,'add']);
    Route::get('/arrange', [$controller,'arrange']);
    Route::post('/arrange/sort', [$controller,'sort']);
    Route::get('/charts', [$controller,'charts']);
    Route::get('/edit/{id}', [$controller,'edit']);
    Route::get('/view/{id}', [$controller,'view']);
    Route::get('/view/{id}/updateSettings', [$controller,'updateSettings']);
    Route::post('/view/{id}/transferDays', [$controller,'transferDays']);
    Route::get('/invLogin/{id}', [$controller,'invLogin']);
    Route::get('/pinCodeLogin/{id}', [$controller,'pinCodeLogin']);
    Route::post('/update/{id}', [$controller,'update']);
    Route::post('/fastEdit', [$controller,'fastEdit']);
	Route::post('/create', [$controller,'create']);
    Route::get('/delete/{id}', [$controller,'delete']);

    /*----------------------------------------------------------
    Images
    ----------------------------------------------------------*/

    Route::post('/add/uploadImage', [$controller,'uploadImage']);
    Route::post('/edit/{id}/editImage', [$controller,'uploadImage']);
    Route::post('/edit/{id}/deleteImage', [$controller,'deleteImage']);
});