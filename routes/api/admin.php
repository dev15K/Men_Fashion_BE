<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\restapi\admin\AdminCategoryApi;
use App\Http\Controllers\restapi\admin\AdminProductApi;

Route::group(['prefix' => 'categories'], function () {
    Route::get('index', [AdminCategoryApi::class, 'list'])->name('api.admin.categories.list');
    Route::get('detail/{id}', [AdminCategoryApi::class, 'detail'])->name('api.admin.categories.detail');
    Route::post('create', [AdminCategoryApi::class, 'create'])->name('api.admin.categories.create');
    Route::post('update/{id}', [AdminCategoryApi::class, 'update'])->name('api.admin.categories.update');
    Route::delete('delete/{id}', [AdminCategoryApi::class, 'delete'])->name('api.admin.categories.delete');
});

Route::group(['prefix' => 'products'], function () {
    Route::get('index', [AdminProductApi::class, 'list'])->name('api.admin.products.list');
    Route::get('detail/{id}', [AdminProductApi::class, 'detail'])->name('api.admin.products.detail');
    Route::post('create', [AdminProductApi::class, 'create'])->name('api.admin.products.create');
    Route::post('update/{id}', [AdminProductApi::class, 'update'])->name('api.admin.products.update');
    Route::delete('delete/{id}', [AdminProductApi::class, 'delete'])->name('api.admin.products.delete');
});
