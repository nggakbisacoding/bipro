<?php

use Modules\Keyword\Http\Controllers\Frontend\KeywordController;

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
    Route::resource('/keyword', KeywordController::class)
        ->middleware('is_user');
});
