<?php 

/*----------------------------------------------------------
ApiMods
----------------------------------------------------------*/
// $controller = \App\Http\Controllers\ExternalServicesControllers::class;
// Route::group(['prefix' => '/services/{service}'] , function () use ($controller) {
//     Route::get('/reports', [$controller,'reports']);

//     Route::get('/templates', [$controller,'templates']);
//     Route::get('/templates/edit/{id}', [$controller,'templatesEdit']);
//     Route::post('/templates/update/{id}', [$controller,'templatesUpdate']);

//     Route::get('/{type}', [$controller,'getServiceData']);
//     Route::get('/{type}/{refresh}', [$controller,'getServiceData']);
// });

Route::group(['prefix' => '/services/salla'] , function (){
	$controller = \App\Http\Controllers\SallaControllers::class;
	Route::get('/customers',[$controller,'customers']);
	Route::get('/products',[$controller,'products']);
	Route::get('/orders',[$controller,'orders']);
	Route::get('/reports', [$controller,'reports']);
	Route::get('/templates', [$controller,'templates']);
	Route::get('/templates/edit/{id}', [$controller,'templatesEdit']);
	Route::post('/templates/update/{id}', [$controller,'templatesUpdate']);
});

Route::group(['prefix' => '/services/zid'] , function (){
	$controller = \App\Http\Controllers\ZidControllers::class;
	Route::get('/customers',[$controller,'customers']);
	Route::get('/products',[$controller,'products']);
	Route::get('/orders',[$controller,'orders']);
	Route::get('/reports', [$controller,'reports']);
	Route::get('/templates', [$controller,'templates']);
	Route::get('/templates/edit/{id}', [$controller,'templatesEdit']);
	Route::post('/templates/update/{id}', [$controller,'templatesUpdate']);
});