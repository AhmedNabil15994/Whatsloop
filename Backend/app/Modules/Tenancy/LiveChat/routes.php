<?php 

/*----------------------------------------------------------
LiveChat
----------------------------------------------------------*/
$controller = \App\Http\Controllers\LiveChatControllers::class;
Route::group(['prefix' => '/livechat'] , function () use ($controller) {
    Route::get('/', [$controller,'index']);
    Route::get('/dialogs', [$controller,'dialogs']);
    Route::post('/pinChat', [$controller,'pinChat']);
    Route::post('/unpinChat', [$controller,'unpinChat']);
    Route::post('/readChat', [$controller,'readChat']);
    Route::post('/unreadChat', [$controller,'unreadChat']);

    Route::get('/messages', [$controller,'messages']);
    
    Route::post('/sendMessage', [$controller,'sendMessage']);
});

Route::group(['prefix' => '/livechatApi'] , function () use ($controller) {
    Route::get('/', [$controller,'index']);
    Route::get('/dialogs', [$controller,'dialogs']);
    Route::post('/pinChat', [$controller,'pinChat']);
    Route::post('/unpinChat', [$controller,'unpinChat']);
    Route::post('/readChat', [$controller,'readChat']);
    Route::post('/unreadChat', [$controller,'unreadChat']);
    
    Route::get('/messages', [$controller,'messages']);
    
    Route::post('/sendMessage', [$controller,'sendMessage']);
});
