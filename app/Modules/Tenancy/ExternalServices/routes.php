<?php 

/*----------------------------------------------------------
ApiMods
----------------------------------------------------------*/
$controller = \App\Http\Controllers\ExternalServicesControllers::class;
Route::group(['prefix' => '/services/{service}'] , function () use ($controller) {
    Route::get('/reports', [$controller,'reports']);

    Route::get('/templates', [$controller,'templates']);
    Route::get('/templates/edit/{id}', [$controller,'templatesEdit']);
    Route::post('/templates/update/{id}', [$controller,'templatesUpdate']);

    Route::get('/{type}', [$controller,'getServiceData']);
    Route::get('/{type}/{refresh}', [$controller,'getServiceData']);
});