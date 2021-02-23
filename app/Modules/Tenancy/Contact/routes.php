<?php 

/*----------------------------------------------------------
Contacts
----------------------------------------------------------*/
$controller = \App\Http\Controllers\ContactsControllers::class;
Route::group(['prefix' => '/contacts'] , function () use ($controller) {
    Route::get('/', [$controller,'index']);
    Route::get('/add', [$controller,'add']);
    Route::get('/arrange', [$controller,'arrange']);
    Route::post('/arrange/sort', [$controller,'sort']);
    Route::get('/charts', [$controller,'charts']);
    Route::get('/edit/{id}', [$controller,'edit']);
    Route::post('/update/{id}', [$controller,'update']);
    Route::post('/fastEdit', [$controller,'fastEdit']);
	Route::post('/create', [$controller,'create']);
    Route::get('/delete/{id}', [$controller,'delete']);
    Route::get('/export/{id}',[$controller,'downloadContacts']);
});