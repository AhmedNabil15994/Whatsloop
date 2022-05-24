<?php 

/*----------------------------------------------------------
Feedback & Ratings
----------------------------------------------------------*/
Route::group(['prefix' => '/feedback'] , function () {
    $controller = \App\Http\Controllers\FeedbackControllers::class;
    Route::get('/', [$controller,'index']);
    Route::get('/add', [$controller,'add']);
    Route::post('/create', [$controller,'create']);

 //    Route::get('/charts', [$controller,'charts']);
 //    Route::get('/view/{id}', [$controller,'view']);
 //    Route::get('/refresh/{id}', [$controller,'refresh']);
 //    Route::get('/resend/{id}/{status}', [$controller,'resend']);

 //    /*----------------------------------------------------------
 //    Images
 //    ----------------------------------------------------------*/

 //    Route::post('/add/uploadImage/{type}', [$controller,'uploadImage']);
});