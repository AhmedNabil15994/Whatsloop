<?php 

/*----------------------------------------------------------
Salla
----------------------------------------------------------*/
Route::group(['prefix' => '/salla'] , function () {
    $controller = \App\Http\Controllers\CentralSallaControllers::class;
    Route::get('/', [$controller,'index']);

    Route::get('/add', [$controller,'add']);
    Route::get('/edit/{id}', [$controller,'edit']);
    Route::post('/update/{id}', [$controller,'update']);
	Route::post('/create', [$controller,'create']);

});