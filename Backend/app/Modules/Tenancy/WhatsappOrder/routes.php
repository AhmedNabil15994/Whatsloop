<?php 

Route::group(['prefix' => '/whatsappOrders'] , function (){
	$controller = \App\Http\Controllers\WhatsappOrdersControllers::class;
	Route::get('/products',[$controller,'products']);
	Route::get('/orders',[$controller,'orders']);
});
