<?php

/*----------------------------------------------------------
Invoices
----------------------------------------------------------*/
Route::group(['prefix' => '/invoices'] , function () {
    Route::get('/', 'InvoiceControllers@index');
    Route::get('/arrange', 'InvoiceControllers@arrange');
    Route::get('/charts', 'InvoiceControllers@charts');
    Route::get('/edit/{id}', 'InvoiceControllers@edit');
    Route::get('/view/{id}', 'InvoiceControllers@view');
    Route::get('/{id}/downloadPDF', 'InvoiceControllers@downloadPDF');
    Route::post('/update/{id}', 'InvoiceControllers@update');
    Route::post('/fastEdit', 'InvoiceControllers@fastEdit');
    Route::get('/delete/{id}', 'InvoiceControllers@delete');
    Route::post('/arrange/sort', 'InvoiceControllers@sort');
});