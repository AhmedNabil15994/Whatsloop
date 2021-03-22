<?php 

/*----------------------------------------------------------
LiveChat
----------------------------------------------------------*/
$controller = \App\Http\Controllers\LiveChatControllers::class;
Route::group(['prefix' => '/livechat'] , function () use ($controller) {
    Route::get('/', [$controller,'index']);
    Route::get('/sendMessage', [$controller,'sendMessage']);
});
