<?php

/*----------------------------------------------------------
Transfers
----------------------------------------------------------*/
Route::group(['prefix' => '/transfers'] , function () {
    Route::get('/', 'TransferRequestControllers@index');
    Route::get('/view/{id}', 'TransferRequestControllers@view');
    Route::post('/update/{id}', 'TransferRequestControllers@update');
    Route::get('/delete/{id}', 'TransferRequestControllers@delete');
});