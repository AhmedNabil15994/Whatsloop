<?php

/*----------------------------------------------------------
Status Categories
----------------------------------------------------------*/
Route::group(['prefix' => '/statuscategories'] , function () {
    Route::get('/', 'StatusCategoryControllers@index');
    Route::get('/add', 'StatusCategoryControllers@add');
    Route::get('/arrange', 'StatusCategoryControllers@arrange');
    Route::get('/charts', 'StatusCategoryControllers@charts');
    Route::get('/edit/{id}', 'StatusCategoryControllers@edit');
    Route::post('/update/{id}', 'StatusCategoryControllers@update');
    Route::post('/fastEdit', 'StatusCategoryControllers@fastEdit');
    Route::post('/create', 'StatusCategoryControllers@create');
    Route::get('/delete/{id}', 'StatusCategoryControllers@delete');
    Route::post('/arrange/sort', 'StatusCategoryControllers@sort');
});