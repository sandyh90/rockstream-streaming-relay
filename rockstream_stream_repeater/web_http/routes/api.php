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

Route::get('/panel/rtmp_stat', [App\Http\Controllers\PanelController::class, 'get_stat_rtmp'])->name('panel.data_rtmp_stat');

Route::options('/premiere/launch_premiere_daemon', [App\Http\Controllers\PremiereVideoController::class, 'launch_queue_daemon'])->name('premiere.launch_daemon');

Route::group(['prefix' => 'fetch'], function () {
    Route::post('/panel/stream_key_encoder', [App\Http\Controllers\PanelController::class, 'get_stream_key'])->name('panel.rtmp_stream_key');
    Route::post('/panel/get_stream_preview', [App\Http\Controllers\PanelController::class, 'check_stream_preview'])->name('panel.rtmp_preview');
    Route::post('/panel/fetch_input_stream', [App\Http\Controllers\PanelController::class, 'fetch_stream_input'])->name('panel.fetch_stream_input');
});

Route::fallback(function () {
    return abort(404);
});
