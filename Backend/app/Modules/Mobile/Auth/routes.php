<?php

/*----------------------------------------------------------
User Auth
----------------------------------------------------------*/
Route::group(['prefix' => '/'] , function () {
    Route::post('login', 'AuthController@login');
    Route::post('checkByCode', 'AuthController@checkByCode');
    Route::post('logout', 'AuthController@logout');
    Route::post('oldClient', 'AuthController@oldClient');

    // Route::post('register', 'AuthController@register');

    // Route::post('get-code', 'AuthController@getCode');
    // Route::post('check-email-code', 'AuthController@checkEmailCode');
    // Route::post('reset-password', 'AuthController@doResetPassword');
});
