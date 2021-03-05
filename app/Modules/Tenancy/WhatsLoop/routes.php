<?php 

/*----------------------------------------------------------
Whatsloop
----------------------------------------------------------*/
Route::group(['prefix' => '/whatsloop'] , function (){
	$controller = \App\Http\Controllers\WhatsloopControllers::class;
	Route::group(['prefix' => '/instances'] ,function() use ($controller){
		Route::get('/status',[$controller,'status']);
		Route::get('/qr_code',[$controller,'qr_code']);
		Route::get('/logout',[$controller,'logout']);
		Route::get('/takeover',[$controller,'takeover']);
		Route::get('/expiry',[$controller,'expiry']);
		Route::get('/retry',[$controller,'retry']);
		Route::get('/reboot',[$controller,'reboot']);
		Route::get('/settings',[$controller,'settings']);
		Route::get('/postSettings',[$controller,'postSettings']);
		Route::get('/outputIP',[$controller,'outputIP']);
		Route::get('/me',[$controller,'me']);
		Route::get('/setName',[$controller,'setName']);
		Route::get('/setStatus',[$controller,'setStatus']);
		Route::get('/repeatHook',[$controller,'repeatHook']);
		Route::get('/labelsList',[$controller,'labelsList']);
		Route::get('/createLabel',[$controller,'createLabel']);
		Route::get('/updateLabel',[$controller,'updateLabel']);
		Route::get('/removeLabel',[$controller,'removeLabel']);
	});

	Route::group(['prefix' => '/webhooks'] ,function() use ($controller){
		Route::get('/webhook',[$controller,'webhook']);
	});

	Route::group(['prefix' => '/queues'] ,function() use ($controller){
		Route::get('/showMessagesQueue',[$controller,'showMessagesQueue']);
		Route::get('/clearMessagesQueue',[$controller,'clearMessagesQueue']);
		Route::get('/showActionsQueue',[$controller,'showActionsQueue']);
		Route::get('/clearActionsQueue',[$controller,'clearActionsQueue']);
	});

	Route::group(['prefix' => '/ban'] ,function() use ($controller){
		Route::get('/banSettings',[$controller,'banSettings']);
		Route::get('/postBanSettings',[$controller,'postBanSettings']);
		Route::get('/banTest',[$controller,'banTest']);
	});

	Route::group(['prefix' => '/testing'] ,function() use ($controller){
		Route::get('/instanceStatuses',[$controller,'instanceStatuses']);
		Route::get('/webhookStatus',[$controller,'webhookStatus']);
		Route::get('/checkPhone',[$controller,'checkPhone']);
	});

	Route::group(['prefix' => '/users'] ,function() use ($controller){
		Route::get('/userStatus',[$controller,'userStatus']);
	});
});
