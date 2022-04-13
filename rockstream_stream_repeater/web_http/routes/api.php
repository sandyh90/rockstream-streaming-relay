<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'stream'], function () {
    Route::post('/on_publish', [App\Http\Controllers\EventStreamController::class, 'on_publish']);
    Route::post('/on_publish_done', [App\Http\Controllers\EventStreamController::class, 'on_publish_done']);
});

Route::fallback(function () {
    return abort(404);
});
