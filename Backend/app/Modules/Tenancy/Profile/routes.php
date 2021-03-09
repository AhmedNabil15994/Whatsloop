<?php 

/*----------------------------------------------------------
Users
----------------------------------------------------------*/
Route::group(['prefix' => '/profile'] , function () {
    $controller = \App\Http\Controllers\ProfileControllers::class;
    Route::get('/', [$controller,'index']);
    
    Route::get('/personalInfo', [$controller,'personalInfo']);
    Route::post('/updatePersonalInfo', [$controller,'updatePersonalInfo']);
    /*----------------------------------------------------------
    Images
    ----------------------------------------------------------*/
    Route::post('/personalInfo/uploadImage', [$controller,'uploadImage']);
    Route::post('/personalInfo/deleteImage', [$controller,'deleteImage']);

    Route::get('/changePassword', [$controller,'changePassword']);
    Route::post('/postChangePassword', [$controller,'postChangePassword']);

    Route::get('/paymentInfo', [$controller,'paymentInfo']);
    Route::post('/postPaymentInfo', [$controller,'postPaymentInfo']);

    Route::get('/taxInfo', [$controller,'taxInfo']);
    Route::post('/postTaxInfo', [$controller,'postTaxInfo']);

    Route::get('/notifications', [$controller,'notifications']);
    Route::post('/postNotifications', [$controller,'postNotifications']);

    Route::get('/offers', [$controller,'offers']);
    Route::post('/postOffers', [$controller,'postOffers']);

    Route::get('/services', [$controller,'services']);
    Route::post('/updateSalla', [$controller,'updateSalla']);
    Route::post('/updateZid', [$controller,'updateZid']);

    Route::get('/subscription', [$controller,'subscription']);

    Route::get('/apiSetting', [$controller,'apiSetting']);
    
    Route::get('/apiGuide', [$controller,'apiGuide']);

    Route::get('/webhookSetting', [$controller,'webhookSetting']);
    Route::post('/postWebhookSetting', [$controller,'postWebhookSetting']);

});