<?php

/*----------------------------------------------------------
Bundle
----------------------------------------------------------*/
Route::group(['prefix' => '/notificationTemplates'] , function () {
    Route::get('/', 'NotificationTemplateControllers@index');
    Route::get('/arrange', 'NotificationTemplateControllers@index@arrange');
    Route::get('/charts', 'NotificationTemplateControllers@charts');
    Route::get('/edit/{id}', 'NotificationTemplateControllers@edit');
    Route::post('/update/{id}', 'NotificationTemplateControllers@update');
    Route::post('/fastEdit', 'NotificationTemplateControllers@fastEdit');
    Route::get('/delete/{id}', 'NotificationTemplateControllers@delete');
    Route::post('/arrange/sort', 'NotificationTemplateControllers@sort');
});