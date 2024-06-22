<?php

use Modules\Project\Http\Controllers\Backend\ProjectController;

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

Route::get('/project/active/{project}', [ProjectController::class, 'activate'])
    ->middleware('is_admin')
    ->name('project.active');
Route::resource('/project', ProjectController::class)
    ->middleware('is_admin');
