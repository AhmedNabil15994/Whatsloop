<?php

/*----------------------------------------------------------
User Auth
----------------------------------------------------------*/
Route::group(['prefix' => '/'] , function () {
    $authController = App\Http\Controllers\AuthControllers::class;

    Route::get('/', function(){return redirect('/login');});

    Route::get('/login', [$authController,'login'])->name('login');

    Route::post('/pushInvoice', [App\Http\Controllers\SubscriptionControllers::class,'pushInvoice']);
    Route::post('/invoices/{id}/pushInvoice', [\App\Http\Controllers\TenantInvoiceControllers::class,'pushInvoice']);
    
    Route::get('/loginByCode', [$authController,'loginByCode'])->name('loginByCode');
    Route::post('/login', [$authController,'doLogin'])->name('doLogin');
    Route::post('/checkByCode', [$authController,'checkByCode']);
    Route::get('/logout', [$authController,'logout']);

    Route::get('/getResetPassword', [$authController,'getResetPassword'])->name('getResetPassword');
    Route::post('/resetPassword', [$authController,'resetPassword'])->name('resetPassword');
    Route::get('/changePassword', [$authController,'changePassword']);
    Route::post('/checkResetPassword', [$authController,'checkResetPassword']);
    Route::post('/completeReset', [$authController,'completeReset']);

    Route::post('/changeLang', [$authController,'changeLang'])->name('changeLang');

    Route::get('impersonate/{token}',[App\Http\Controllers\ImpersonatesController::class, 'index'])->name('impersonate');
});


Route::get('/whatsappOrders/orders/{id}',[App\Http\Controllers\WhatsappOrdersControllers::class,'getOneOrder']);
Route::get('/whatsappOrders/orders/{id}/info',[App\Http\Controllers\WhatsappOrdersControllers::class,'setOrderIno']);