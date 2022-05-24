<?php 

Route::group(['prefix' => '/whatsappOrders'] , function (){
	$controller = \App\Http\Controllers\WhatsappOrdersControllers::class;
	Route::get('/settings',[$controller,'settings']);
	Route::post('/settings',[$controller,'postSettings']);
	
	Route::get('/bankTransfers',[$controller,'bankTransfers']);
	Route::get('/bankTransfers/view/{id}', [$controller,'viewTransfer']);
    Route::post('/bankTransfers/update/{id}', [$controller,'updateTransfer']);
    Route::get('/bankTransfers/delete/{id}', [$controller,'deleteTransfer']);

	Route::get('/products',[$controller,'products']);
	Route::post('/products/assignCategory',[$controller,'assignCategory']);
	Route::post('/products/assignSallaProduct',[$controller,'assignSallaProduct']);
	Route::get('/orders',[$controller,'orders']);
	Route::get('/orders/{id}/sendLink',[$controller,'sendLink']);




	/*----------------------------------------------------------
	Coupons
	----------------------------------------------------------*/
	Route::group(['prefix' => '/coupons'] , function () {
		$controller2 = \App\Http\Controllers\WhatsAppCouponControllers::class;

	    Route::get('/', [$controller2,'index']);
	    Route::get('/add', [$controller2,'add']);
	    Route::get('/arrange', [$controller2,'arrange']);
	    Route::get('/charts', [$controller2,'charts']);
	    Route::get('/edit/{id}', [$controller2,'edit']);
	    Route::post('/update/{id}', [$controller2,'update']);
	    Route::post('/fastEdit', [$controller2,'fastEdit']);
		Route::post('/create', [$controller2,'create']);
	    Route::get('/delete/{id}', [$controller2,'delete']);
	    Route::post('/arrange/sort', [$controller2,'sort']);
	});
});
