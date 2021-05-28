<?php 

/*----------------------------------------------------------
Groups
----------------------------------------------------------*/
Route::group(['prefix' => '/groups'] , function () {
    $controller = \App\Http\Controllers\CentralGroupsControllers::class;
    Route::get('/', [$controller,'index']);
    Route::get('/add', [$controller,'add']);
    Route::get('/arrange', [$controller,'arrange']);
    Route::get('/charts', [$controller,'charts']);
    Route::get('/edit/{id}', [$controller,'edit']);
    Route::post('/update/{id}', [$controller,'update']);
    Route::post('/fastEdit', [$controller,'fastEdit']);
	Route::post('/create', [$controller,'create']);
    Route::get('/delete/{id}', [$controller,'delete']);
    Route::post('/arrange/sort', [$controller,'sort']);
});