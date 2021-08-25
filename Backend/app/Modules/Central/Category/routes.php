<?php

/*----------------------------------------------------------
Categories
----------------------------------------------------------*/
Route::group(['prefix' => '/categories'] , function () {
    Route::get('/', 'CentralCategoryControllers@index');
    Route::get('/add', 'CentralCategoryControllers@add');
    Route::get('/arrange', 'CentralCategoryControllers@arrange');
    Route::get('/charts', 'CentralCategoryControllers@charts');
    Route::get('/edit/{id}', 'CentralCategoryControllers@edit');
    Route::post('/update/{id}', 'CentralCategoryControllers@update');
    Route::post('/fastEdit', 'CentralCategoryControllers@fastEdit');
    Route::post('/create', 'CentralCategoryControllers@create');
    Route::get('/delete/{id}', 'CentralCategoryControllers@delete');
    Route::post('/arrange/sort', 'CentralCategoryControllers@sort');
});