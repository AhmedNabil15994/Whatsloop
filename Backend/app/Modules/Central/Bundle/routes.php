<?php

/*----------------------------------------------------------
Bundle
----------------------------------------------------------*/
Route::group(['prefix' => '/bundles'] , function () {
    Route::get('/', 'BundleControllers@index');
    Route::get('/add', 'BundleControllers@add');
    Route::get('/arrange', 'BundleControllers@arrange');
    Route::get('/charts', 'BundleControllers@charts');
    Route::get('/edit/{id}', 'BundleControllers@edit');
    Route::post('/update/{id}', 'BundleControllers@update');
    Route::post('/fastEdit', 'BundleControllers@fastEdit');
	Route::post('/create', 'BundleControllers@create');
    Route::get('/delete/{id}', 'BundleControllers@delete');
    Route::post('/arrange/sort', 'BundleControllers@sort');
});