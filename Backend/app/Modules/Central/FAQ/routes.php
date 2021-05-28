<?php 

/*----------------------------------------------------------
Faqs
----------------------------------------------------------*/
Route::group(['prefix' => '/faqs'] , function () {
    $controller = \App\Http\Controllers\FAQControllers::class;
    Route::get('/', [$controller,'index']);
    Route::get('/add', [$controller,'add']);
    Route::get('/arrange', [$controller,'arrange']);
    Route::post('/arrange/sort', [$controller,'sort']);
    Route::get('/charts', [$controller,'charts']);
    Route::get('/edit/{id}', [$controller,'edit']);
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