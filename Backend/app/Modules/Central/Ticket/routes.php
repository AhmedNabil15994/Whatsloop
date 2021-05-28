<?php 

/*----------------------------------------------------------
Ticket
----------------------------------------------------------*/
Route::group(['prefix' => '/tickets'] , function () {
    $controller = \App\Http\Controllers\CentralTicketControllers::class;
    Route::get('/', [$controller,'index']);
    Route::get('/add', [$controller,'add']);
    Route::get('/arrange', [$controller,'arrange']);
    Route::post('/arrange/sort', [$controller,'sort']);
    Route::get('/charts', [$controller,'charts']);
    Route::get('/edit/{id}', [$controller,'edit']);
    Route::get('/view/{id}', [$controller,'view']);
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


    /*----------------------------------------------------------
    Comments
    ----------------------------------------------------------*/
    Route::post('/view/{id}/addComment', [$controller,'addComment']);
    Route::post('/view/{id}/updateComment', [$controller,'updateComment']);
    Route::get('/view/{id}/removeComment/{commentId}', [$controller,'removeComment']);


});