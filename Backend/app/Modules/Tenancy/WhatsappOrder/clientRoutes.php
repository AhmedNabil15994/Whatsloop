<?php 

Route::group(['prefix' => '/orders'] , function (){
	$controller = \App\Http\Controllers\WhatsappOrdersControllers::class;
	Route::get('/{id}/view',[$controller,'getOneOrder']);
	Route::post('/{id}/view',[$controller,'postNewOrder']);
	Route::post('/checkCoupon',[$controller,'checkCoupon']);

	Route::get('/{id}/personalInfo',[$controller,'personalInfo']);
	Route::post('/{id}/personalInfo',[$controller,'postPersonalInfo']);

	Route::get('/{id}/paymentInfo',[$controller,'paymentInfo']);
    Route::get('/getCities', [$controller,'getCities']);
	Route::post('/{id}/paymentInfo',[$controller,'postPaymentInfo']);

	Route::get('/{id}/setPaymentType',[$controller,'setPaymentType']);
	Route::post('/{id}/setPaymentType',[$controller,'postPaymentType']);

	Route::get('/{id}/completeOrder',[$controller,'completeOrder']);
	Route::post('/{id}/bankTransfer',[$controller,'bankTransfer']);

	Route::get('/{id}/finish',[$controller,'finish']);

	Route::post('/pushInvoice/{id}',[$controller,'pushInvoice']);

	Route::get('/{id}/invoice',[$controller,'invoice']);

});
