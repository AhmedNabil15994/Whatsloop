<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => '/clients'] , function () {
    $controller = 'App\Http\Controllers\HomeController';
    Route::get('/', $controller .'@getClients');
    Route::post('/addClient', $controller.'@addClient');
    Route::post('/updateClient', $controller.'@updateClient');
});

Route::group(['prefix' => '/orders'] , function () {
    $controller = 'App\Http\Controllers\HomeController';
    Route::get('/', $controller .'@getOrders');
    Route::post('/addOrder', $controller.'@addOrder');
    Route::post('/acceptOrder', $controller .'@acceptOrder');
    Route::post('/getClientProduct', $controller.'@getClientProduct');
    Route::post('/updateClientProduct', $controller.'@updateClientProduct');
});

Route::group(['prefix' => '/invoices'] , function () {
    $controller = 'App\Http\Controllers\HomeController';
    Route::get('/', $controller .'@getInvoices');
    Route::get('/getInvoice', $controller .'@getInvoice');
    Route::post('/addTransaction', $controller .'@addTransaction');
    Route::post('/addInvoice', $controller.'@addInvoice');
    Route::post('/addInvoicePayment', $controller.'@addInvoicePayment');
    Route::post('/updateInvoice', $controller.'@updateInvoice');
});

Route::group(['prefix' => '/products'] , function () {
    $controller = 'App\Http\Controllers\HomeController';
    Route::get('/', $controller .'@getProducts');
});