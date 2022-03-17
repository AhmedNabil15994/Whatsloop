<?php 

/*----------------------------------------------------------
Clients
----------------------------------------------------------*/
Route::group(['prefix' => '/clients'] , function () {
    $controller = \App\Http\Controllers\ClientControllers::class;
    Route::get('/', [$controller,'index']);
    Route::get('/add', [$controller,'add']);
    Route::get('/arrange', [$controller,'arrange']);
    Route::post('/arrange/sort', [$controller,'sort']);
    Route::get('/charts', [$controller,'charts']);
    Route::get('/edit/{id}', [$controller,'edit']);
    Route::get('/view/{id}', [$controller,'view']);
    Route::get('/view/{id}/updateSettings', [$controller,'updateSettings']);
    Route::post('/view/{id}/transferDays', [$controller,'transferDays']);
    Route::get('/invLogin/{id}', [$controller,'invLogin']);
    Route::get('/pinCodeLogin/{id}', [$controller,'pinCodeLogin']);
    Route::post('/update/{id}', [$controller,'update']);
    Route::post('/fastEdit', [$controller,'fastEdit']);
	Route::post('/create', [$controller,'create']);
    Route::get('/delete/{id}', [$controller,'delete']);

    /*----------------------------------------------------------
    Images
    ----------------------------------------------------------*/

    Route::post('/add/uploadImage', [$controller,'uploadImage']);
    Route::post('/edit/{id}/editImage', [$controller,'uploadImage']);
    Route::post('/edit/{id}/deleteImage', [$controller,'deleteImage']);


    Route::get('/transferDay', [$controller,'transferDay']);
    Route::get('/pushAddonSetting', [$controller,'pushAddonSetting']);
    Route::get('/pushChannelSetting', [$controller,'pushChannelSetting']);
    Route::get('/setInvoices', [$controller,'setInvoices']);


    Route::get('/view/{id}/screenshot', [$controller,'screenshot']);
    Route::get('/view/{id}/reconnect', [$controller,'reconnect']);
    Route::get('/view/{id}/closeConn', [$controller,'closeConn']);
    Route::get('/view/{id}/sync', [$controller,'sync']);
    Route::get('/view/{id}/syncAll', [$controller,'syncAll']);
    Route::get('/view/{id}/syncDialogs', [$controller,'syncDialogs']);
    Route::get('/view/{id}/syncLabels', [$controller,'syncLabels']);
    Route::get('/view/{id}/syncOrdersProducts', [$controller,'syncOrdersProducts']);
    Route::get('/view/{id}/restoreAccountSettings', [$controller,'restoreAccountSettings']);
    Route::get('/view/{id}/read/{status}', [$controller,'read']);
    Route::get('/view/{id}/clearMessagesQueue', [$controller,'clearMessagesQueue']);

});