<?php

use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'guest', 'prefix' => 'setup'], function () {
    Route::get('/', [App\Http\Controllers\SetupAppController::class, 'index'])->name('setup');
    Route::post('/setup', [App\Http\Controllers\SetupAppController::class, 'setup_app'])->name('setup.process');
});

Route::group(['middleware' => 'setup.state'], function () {
    Route::group(['middleware' => 'guest', 'prefix' => 'auth'], function () {
        Route::get('/', [App\Http\Controllers\Auth\AuthController::class, 'showlogin'])->name('login');
        Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login.process');
        Route::get('/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showresetpassword'])->name('reset');
        Route::post('/resetpassword', [App\Http\Controllers\Auth\ResetPasswordController::class, 'resetpassword'])->name('reset.process');
    });

    Route::group(['middleware' => 'auth'], function () {
        Route::post('logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

        Route::get('/', [App\Http\Controllers\PanelController::class, 'index'])->name('home');
        Route::post('/control_stream', [App\Http\Controllers\PanelController::class, 'control_server_panel'])->name('home.control_server');

        Route::prefix('settings')->group(function () {
            Route::get('/', [App\Http\Controllers\AccountSettingsController::class, 'settings'])->name('user.settings');
            Route::post('/profile_change_data', [App\Http\Controllers\AccountSettingsController::class, 'update_profile'])->name('settings.update_profile');
            Route::post('/password_change_data', [App\Http\Controllers\AccountSettingsController::class, 'update_password'])->name('settings.update_password');
            Route::post('/logout_all_session', [App\Http\Controllers\AccountSettingsController::class, 'logout_all_session'])->name('settings.logout_all');
            Route::post('/reset_factory', [App\Http\Controllers\AccountSettingsController::class, 'reset_factory'])->name('settings.reset_factory');
        });

        Route::group(['prefix' => 'stream'], function () {
            Route::get('/', [App\Http\Controllers\IngestStreamController::class, 'index'])->name('stream.home');
            Route::get('/manage/{id_stream?}', [App\Http\Controllers\IngestStreamController::class, 'manage_input_stream'])->name('stream.manage');
            Route::get('/getdata', [App\Http\Controllers\IngestStreamController::class, 'get_input_stream'])->name('stream.getdata');
            Route::post('/regenstreamkey', [App\Http\Controllers\IngestStreamController::class, 'regen_stream_key'])->name('stream.regenstreamkey');
            Route::post('/add', [App\Http\Controllers\IngestStreamController::class, 'add_input_stream'])->name('stream.add');
            Route::post('/edit', [App\Http\Controllers\IngestStreamController::class, 'edit_input_stream'])->name('stream.edit');
            Route::post('/delete', [App\Http\Controllers\IngestStreamController::class, 'delete_input_stream'])->name('stream.delete');
        });

        Route::group(['prefix' => 'output'], function () {
            Route::get('/getdata', [App\Http\Controllers\IngestDestinationController::class, 'get_output_dest'])->name('outputdest.getdata');
            Route::post('/view', [App\Http\Controllers\IngestDestinationController::class, 'view_output_dest'])->name('outputdest.view');
            Route::post('/add', [App\Http\Controllers\IngestDestinationController::class, 'add_output_dest'])->name('outputdest.add');
            Route::post('/edit', [App\Http\Controllers\IngestDestinationController::class, 'edit_output_dest'])->name('outputdest.edit');
            Route::post('/delete', [App\Http\Controllers\IngestDestinationController::class, 'delete_output_dest'])->name('outputdest.delete');
        });
    });
});

Route::fallback(function () {
    return abort(404);
});
