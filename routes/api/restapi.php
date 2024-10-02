<?php

/*
|--------------------------------------------------------------------------
| RestApi Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\restapi\ProductApi;

Route::group(['prefix' => 'products'], function () {
    Route::get('index', [ProductApi::class, 'list'])->name('api.restapi.products.list');
    Route::get('detail/{id}', [ProductApi::class, 'detail'])->name('api.restapi.products.detail');
    Route::get('search', [ProductApi::class, 'search'])->name('api.restapi.products.search');
});
