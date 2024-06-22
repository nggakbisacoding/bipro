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

use Modules\Auth\Http\Controllers\Backend\Role\RoleController;
use Modules\Auth\Http\Controllers\Backend\User\UserController;

Route::get('/users/impersonate/{user}', [UserController::class, 'impersonate'])
    ->middleware('is_admin')
    ->name('users.impersonate');
Route::resource('/users', UserController::class)
    ->middleware('is_admin');
Route::resource('/users', UserController::class)
    ->middleware('is_admin');
Route::resource('/roles', RoleController::class)
    ->middleware('is_admin');
