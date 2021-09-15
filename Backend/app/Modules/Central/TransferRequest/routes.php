<?php

/*----------------------------------------------------------
Transfers
----------------------------------------------------------*/
Route::group(['prefix' => '/transfers'] , function () {
    Route::get('/', 'TransferRequestControllers@index');
    Route::get('/add', 'TransferRequestControllers@add');
    Route::get('/arrange', 'TransferRequestControllers@arrange');
    Route::get('/charts', 'TransferRequestControllers@charts');
    Route::get('/edit/{id}', 'TransferRequestControllers@edit');
    Route::post('/update/{id}', 'TransferRequestControllers@update');
    Route::post('/fastEdit', 'TransferRequestControllers@fastEdit');
    Route::post('/create', 'TransferRequestControllers@create');
    Route::get('/delete/{id}', 'TransferRequestControllers@delete');
    Route::post('/arrange/sort', 'TransferRequestControllers@sort');
});