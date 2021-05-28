<?php

/*----------------------------------------------------------
Addons
----------------------------------------------------------*/
Route::group(['prefix' => '/addons'] , function () {
    Route::get('/', 'AddonsControllers@index');
    Route::get('/add', 'AddonsControllers@add');
    Route::get('/arrange', 'AddonsControllers@arrange');
    Route::get('/charts', 'AddonsControllers@charts');
    Route::get('/edit/{id}', 'AddonsControllers@edit');
    Route::post('/update/{id}', 'AddonsControllers@update');
    Route::post('/fastEdit', 'AddonsControllers@fastEdit');
	Route::post('/create', 'AddonsControllers@create');
    Route::get('/delete/{id}', 'AddonsControllers@delete');
    Route::post('/arrange/sort', 'AddonsControllers@sort');
});