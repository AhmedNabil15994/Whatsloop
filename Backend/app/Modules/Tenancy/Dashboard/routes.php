<?php

/*----------------------------------------------------------
Dashboard
----------------------------------------------------------*/
Route::group(['prefix' => '/'] , function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardControllers::class,'Dashboard'])->name('userDash');
    Route::get('/menu', [App\Http\Controllers\DashboardControllers::class,'menu']);

    Route::post('/changeChannel', [App\Http\Controllers\DashboardControllers::class,'changeChannel']);
    
    Route::post('/changeTheme', [App\Http\Controllers\DashboardControllers::class,'changeTheme']);
    Route::post('/changeTheme/default', [App\Http\Controllers\DashboardControllers::class,'changeThemeToDefault']);

    Route::get('/faq', [App\Http\Controllers\DashboardControllers::class,'faqs']);
    Route::get('/helpCenter', [App\Http\Controllers\DashboardControllers::class,'helpCenter']);
    Route::post('/helpCenter/addRate', [App\Http\Controllers\DashboardControllers::class,'addRate']);


    // Route::get('/checkout', [App\Http\Controllers\DashboardControllers::class,'checkout'])->name('checkout');
    // Route::post('/checkout', [App\Http\Controllers\DashboardControllers::class,'postCheckout']);
    // Route::post('/completeOrder', [App\Http\Controllers\DashboardControllers::class,'completeOrder']);
    
    // Route::get('/QR', [App\Http\Controllers\DashboardControllers::class,'qrIndex']);
    // Route::post('/QR/updateName', [App\Http\Controllers\DashboardControllers::class,'updateName']);
    // Route::post('/QR/getQR', [App\Http\Controllers\DashboardControllers::class,'getQR']);
    // Route::get('/QR/finish/{modID}', [App\Http\Controllers\DashboardControllers::class,'finishModID']);
    // Route::post('/QR/editTemplate', [App\Http\Controllers\DashboardControllers::class,'editTemplate']);


});

/*----------------------------------------------------------
Subscription
----------------------------------------------------------*/
Route::group(['prefix' => '/'] , function () {

    Route::get('/packages', [App\Http\Controllers\SubscriptionControllers::class,'packages']);
    
    Route::get('/updateSubscription', [App\Http\Controllers\SubscriptionControllers::class,'updateSubscription']);
    Route::post('/updateSubscription', [App\Http\Controllers\SubscriptionControllers::class,'postUpdateSubscription'])->name('postCheckout');

    Route::get('/checkout', [App\Http\Controllers\SubscriptionControllers::class,'checkout'])->name('checkout');
    Route::post('/checkout', [App\Http\Controllers\SubscriptionControllers::class,'postCheckout']);
    Route::post('/completeOrder', [App\Http\Controllers\SubscriptionControllers::class,'completeOrder']);
    Route::get('/QR', [App\Http\Controllers\SubscriptionControllers::class,'qrIndex']);
    Route::post('/QR/updateName', [App\Http\Controllers\SubscriptionControllers::class,'updateName']);
    Route::post('/QR/getQR', [App\Http\Controllers\SubscriptionControllers::class,'getQR']);
    Route::get('/QR/finish/{modID}', [App\Http\Controllers\SubscriptionControllers::class,'finishModID']);
    Route::post('/QR/editTemplate', [App\Http\Controllers\SubscriptionControllers::class,'editTemplate']);


    Route::get('/updateAddonStatus/{addon_id}/{status}', [App\Http\Controllers\SubscriptionControllers::class,'updateAddonStatus']);
    Route::get('/updateQuotaStatus/{extra_quota_id}/{status}', [App\Http\Controllers\SubscriptionControllers::class,'updateQuotaStatus']);
});