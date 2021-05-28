<?php

/*----------------------------------------------------------
Extra Quotas
----------------------------------------------------------*/
Route::group(['prefix' => '/extraQuotas'] , function () {
    Route::get('/', 'ExtraQuotaControllers@index');
    Route::get('/add', 'ExtraQuotaControllers@add');
    Route::get('/arrange', 'ExtraQuotaControllers@arrange');
    Route::get('/charts', 'ExtraQuotaControllers@charts');
    Route::get('/edit/{id}', 'ExtraQuotaControllers@edit');
    Route::post('/update/{id}', 'ExtraQuotaControllers@update');
    Route::post('/fastEdit', 'ExtraQuotaControllers@fastEdit');
	Route::post('/create', 'ExtraQuotaControllers@create');
    Route::get('/delete/{id}', 'ExtraQuotaControllers@delete');
    Route::post('/arrange/sort', 'ExtraQuotaControllers@sort');
});