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

use Modules\Post\Http\Controllers\Backend\PostController;
use Modules\Post\Http\Controllers\Backend\PostExportController;

Route::group(['as' => 'user.', 'middleware' => ['auth', 'is_user']], function () {
    Route::get('/post/exports', [PostExportController::class, 'index'])->name('post.export.index');
    Route::post('/post/exports', [PostExportController::class, 'store'])->name('post.export.store');
    Route::get('/post/exports/download/{id}', [PostExportController::class, 'download'])->name('post.export.download');
    Route::resource('/post', PostController::class)
        ->only(['index', 'show']);
});
