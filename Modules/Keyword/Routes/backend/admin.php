<?php

use Modules\Keyword\Http\Controllers\Backend\KeywordController;

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

Route::get('/keyword/export', [KeywordController::class, 'export_template'])
    ->name('keyword.export.template')
    ->middleware('is_admin');
Route::post('/keyword/import', [KeywordController::class, 'import'])
    ->name('keyword.import')
    ->middleware('is_admin');
Route::resource('/keyword', KeywordController::class)
    ->middleware('is_admin');
