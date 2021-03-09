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

	Route::group(['prefix' => '/messages'] ,function() use ($controller){
		Route::get('/sendMessage',[$controller,'sendMessage']);
		Route::get('/sendFile',[$controller,'sendFile']);
		Route::get('/sendPTT',[$controller,'sendPTT']);
		Route::get('/sendLink',[$controller,'sendLink']);
		Route::get('/sendContact',[$controller,'sendContact']);
		Route::get('/sendLocation',[$controller,'sendLocation']);
		Route::get('/sendVCard',[$controller,'sendVCard']);
		Route::get('/forwardMessage',[$controller,'forwardMessage']);
		Route::get('/messages',[$controller,'messages']);
		Route::get('/messagesHistory',[$controller,'messagesHistory']);
		Route::get('/deleteMessage',[$controller,'deleteMessage']);
	});

	Route::group(['prefix' => '/dialogs'] ,function() use ($controller){
		Route::get('/',[$controller,'dialogs']);
		Route::get('/dialog',[$controller,'dialog']);
		Route::get('/group',[$controller,'group']);
		Route::get('/pinChat',[$controller,'pinChat']);
		Route::get('/unpinChat',[$controller,'unpinChat']);
		Route::get('/readChat',[$controller,'readChat']);
		Route::get('/unreadChat',[$controller,'unreadChat']);
		Route::get('/joinGroup',[$controller,'joinGroup']);
		Route::get('/leaveGroup',[$controller,'leaveGroup']);
		Route::get('/removeChat',[$controller,'removeChat']);
		Route::get('/addGroupParticipant',[$controller,'addGroupParticipant']);
		Route::get('/removeGroupParticipant',[$controller,'removeGroupParticipant']);
		Route::get('/promoteGroupParticipant',[$controller,'promoteGroupParticipant']);
		Route::get('/demoteGroupParticipant',[$controller,'demoteGroupParticipant']);
		Route::get('/typing',[$controller,'typing']);
		Route::get('/recording',[$controller,'recording']);
		Route::get('/labelChat',[$controller,'labelChat']);
		Route::get('/unlabelChat',[$controller,'unlabelChat']);
	});

	Route::group(['prefix' => '/webhooks'] ,function() use ($controller){
		Route::get('/webhook',[$controller,'webhook']);
		Route::webhooks('/messages-webhook');
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
