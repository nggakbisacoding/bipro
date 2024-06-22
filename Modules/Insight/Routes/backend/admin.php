<?php

use Modules\Insight\Http\Controllers\Backend\InsightController;

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

Route::get('/insight', [InsightController::class, 'index'])
    ->name('insight.index')
    ->middleware('is_admin');
