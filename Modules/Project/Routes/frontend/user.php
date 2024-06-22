<?php

use Illuminate\Http\Request;
use Modules\Project\Http\Controllers\Frontend\ProjectController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['as' => 'user.', 'middleware' => ['auth']], function () {
    Route::resource('/project', ProjectController::class)
        ->middleware('is_user');
});