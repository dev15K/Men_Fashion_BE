<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\restapi\UserApi;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'users'], function () {
    Route::get('get-info', [UserApi::class, 'getUserFromToken'])->name('api.auth.users.information');
});
