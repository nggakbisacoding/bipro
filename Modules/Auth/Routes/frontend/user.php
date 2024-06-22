<?php

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

use Modules\Auth\Http\Controllers\Frontend\User\UserController;

Route::group(['as' => 'user.', 'middleware' => ['auth']], function () {
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('is_user')
        ->name('users');
});