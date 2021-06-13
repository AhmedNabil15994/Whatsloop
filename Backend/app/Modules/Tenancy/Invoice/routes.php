<?php

/*----------------------------------------------------------
Invoices
----------------------------------------------------------*/
Route::group(['prefix' => '/invoices'] , function () {
    $controller = \App\Http\Controllers\TenantInvoiceControllers::class;

    Route::get('/', [$controller,'index']);
    Route::get('/arrange', [$controller,'arrange']);
    Route::get('/charts', [$controller,'charts']);
    Route::get('/edit/{id}', [$controller,'edit']);
    Route::get('/view/{id}', [$controller,'view']);
    Route::get('/view/{id}/checkout', [$controller,'checkout']);
    Route::post('/update/{id}', [$controller,'update']);
    Route::post('/fastEdit', [$controller,'fastEdit']);
    Route::get('/delete/{id}', [$controller,'delete']);
    Route::post('/arrange/sort', [$controller,'sort']);
});