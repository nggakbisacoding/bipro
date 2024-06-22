<?php

use Modules\Post\Http\Controllers\Backend\PostController;
use Modules\Post\Http\Controllers\Backend\PostExportController;

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

Route::get('/post/exports', [PostExportController::class, 'index'])->name('post.export.index');
Route::post('/post/exports', [PostExportController::class, 'store'])->name('post.export.store');
Route::get('/post/exports/download/{id}', [PostExportController::class, 'download'])->name('post.export.download');
Route::get('/post/tag/{post}', [PostController::class, 'show'])->name('post.show.tag');
Route::get('/post/user/{post}', [PostController::class, 'show'])->name('post.show.user');
Route::get('/post/search/{post}', [PostController::class, 'show'])->name('post.show.keyword');

Route::resource('/post', PostController::class)
    ->only(['index'])
    ->middleware('is_admin');
