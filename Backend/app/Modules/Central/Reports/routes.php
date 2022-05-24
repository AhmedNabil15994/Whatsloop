<?php

/*----------------------------------------------------------
Reports
----------------------------------------------------------*/
Route::group(['prefix' => '/reports'] , function () {
    Route::get('/zid', 'ReportsControllers@zid');
    Route::get('/salla', 'ReportsControllers@salla');
    Route::get('/updateData/{type}', 'ReportsControllers@updateData');
});