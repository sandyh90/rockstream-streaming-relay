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
    Route::group(['middleware' => 'only.ajax'], function () {
        Route::options('/generate_appkey', [App\Http\Controllers\SetupAppController::class, 'generate_appkey'])->name('setup.generate_appkey');
    });
});

Route::group(['middleware' => 'setup.state'], function () {
    Route::group(['middleware' => 'guest', 'prefix' => 'auth'], function () {
        Route::get('/', [App\Http\Controllers\Auth\AuthController::class, 'showlogin'])->name('login');
        Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login.process');
        Route::get('/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showresetpassword'])->name('reset');
        Route::post('/resetpassword', [App\Http\Controllers\Auth\ResetPasswordController::class, 'resetpassword'])->name('reset.process');
    });

    Route::get('/test', [App\Http\Controllers\PanelController::class, 'testapp'])->name('test');
    Route::group(['middleware' => 'auth', 'auth.session'], function () {
        Route::post('logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

        Route::get('/', [App\Http\Controllers\PanelController::class, 'index'])->name('home');
        Route::post('/control_stream', [App\Http\Controllers\PanelController::class, 'control_server_panel'])->name('home.control_server');

        Route::prefix('settings')->group(function () {
            Route::get('/', [App\Http\Controllers\AccountSettingsController::class, 'settings'])->name('user.settings');
            Route::post('/profile_change_data', [App\Http\Controllers\AccountSettingsController::class, 'update_profile'])->name('settings.update_profile');
            Route::post('/password_change_data', [App\Http\Controllers\AccountSettingsController::class, 'update_password'])->name('settings.update_password');
            Route::post('/logout_all_session', [App\Http\Controllers\AccountSettingsController::class, 'logout_all_session'])->name('settings.logout_all');
        });

        Route::group(['middleware' => 'op.access'], function () {
            Route::group(['prefix' => 'users'], function () {
                Route::get('/', [App\Http\Controllers\ManageUsersController::class, 'index'])->name('users.home');
                Route::get('/getdata', [App\Http\Controllers\ManageUsersController::class, 'get_manage_users'])->name('users.getdata');
                Route::post('/add', [App\Http\Controllers\ManageUsersController::class, 'add_users_data'])->name('users.add');
                Route::post('/view', [App\Http\Controllers\ManageUsersController::class, 'view_users_data'])->name('users.view');
                Route::post('/edit', [App\Http\Controllers\ManageUsersController::class, 'edit_users_data'])->name('users.edit');
                Route::post('/delete', [App\Http\Controllers\ManageUsersController::class, 'delete_users_data'])->name('users.delete');
            });

            Route::group(['prefix' => 'analytics'], function () {
                Route::get('/', [App\Http\Controllers\LiveAnalyticsStatController::class, 'index'])->name('analytics.home');
                Route::group(['middleware' => 'only.ajax'], function () {
                    Route::get('/getdata', [App\Http\Controllers\LiveAnalyticsStatController::class, 'get_live_data'])->name('analytics.getdata');
                });
            });

            Route::group(['prefix' => 'diagnostic'], function () {
                Route::get('/', [App\Http\Controllers\DiagnosticController::class, 'index'])->name('diagnostic.home');
                Route::group(['middleware' => 'only.ajax'], function () {
                    Route::get('/getdata_failed_queue', [App\Http\Controllers\DiagnosticController::class, 'get_failed_queue_data'])->name('diagnostic.getdata.failed_queue');
                    Route::post('/view_failed_queue', [App\Http\Controllers\DiagnosticController::class, 'view_failed_queue'])->name('diagnostic.view.failed_queue');
                    Route::post('/delete_failed_queue', [App\Http\Controllers\DiagnosticController::class, 'delete_failed_queue'])->name('diagnostic.delete.failed_queue');
                });
            });

            Route::group(['prefix' => 'interfaces'], function () {
                Route::get('/', [App\Http\Controllers\InterfaceSettingsController::class, 'index'])->name('interfaces.home');
                Route::group(['middleware' => 'only.ajax'], function () {
                    Route::post('/starttestingstream', [App\Http\Controllers\InterfaceSettingsController::class, 'start_testing_streaming'])->name('interfaces.start_teststream');
                    Route::options('/launch_teststream_daemon', [App\Http\Controllers\InterfaceSettingsController::class, 'launch_test_streaming_daemon'])->name('interfaces.launch_daemon_teststream');
                    Route::post('/reset_factory', [App\Http\Controllers\InterfaceSettingsController::class, 'reset_factory'])->name('interfaces.reset_factory');
                    Route::post('/edit_app_settings', [App\Http\Controllers\InterfaceSettingsController::class, 'edit_app_settings'])->name('interfaces.edit_app_settings');
                });
            });
        });

        Route::group(['prefix' => 'premiere'], function () {
            Route::get('/', [App\Http\Controllers\PremiereVideoController::class, 'index'])->name('premiere.home');
            Route::group(['middleware' => 'only.ajax'], function () {
                Route::get('/getdata', [App\Http\Controllers\PremiereVideoController::class, 'get_premiere_video'])->name('premiere.getdata');
                Route::post('/add', [App\Http\Controllers\PremiereVideoController::class, 'add_premiere_video'])->name('premiere.add');
                Route::post('/view', [App\Http\Controllers\PremiereVideoController::class, 'view_premiere_video'])->name('premiere.view');
                Route::post('/delete', [App\Http\Controllers\PremiereVideoController::class, 'delete_premiere_video'])->name('premiere.delete');
                Route::post('/forcestatus', [App\Http\Controllers\PremiereVideoController::class, 'force_status_premiere_video'])->name('premiere.force_status');
                Route::post('/edit', [App\Http\Controllers\PremiereVideoController::class, 'edit_premiere_video'])->name('premiere.edit');
                Route::post('/startpremiere', [App\Http\Controllers\PremiereVideoController::class, 'start_premiere_video'])->name('premiere.start_play');
                Route::options('/launch_premiere_daemon', [App\Http\Controllers\PremiereVideoController::class, 'launch_queue_daemon'])->name('premiere.launch_daemon');
                Route::group(['prefix' => 'queue'], function () {
                    Route::get('/getdata', [App\Http\Controllers\PremiereVideoController::class, 'get_premiere_queue'])->name('premiere.getdata.queue');
                    Route::post('/view', [App\Http\Controllers\PremiereVideoController::class, 'view_premiere_queue'])->name('premiere.view.queue');
                    Route::post('/edit', [App\Http\Controllers\PremiereVideoController::class, 'edit_premiere_queue'])->name('premiere.edit.queue');
                    Route::post('/delete', [App\Http\Controllers\PremiereVideoController::class, 'delete_premiere_queue'])->name('premiere.delete.queue');
                });
            });
        });

        Route::group(['prefix' => 'stream'], function () {
            Route::get('/', [App\Http\Controllers\IngestStreamController::class, 'index'])->name('stream.home');
            Route::get('/manage/{id_stream?}', [App\Http\Controllers\IngestStreamController::class, 'manage_input_stream'])->name('stream.manage');
            Route::group(['middleware' => 'only.ajax'], function () {
                Route::get('/getdata', [App\Http\Controllers\IngestStreamController::class, 'get_input_stream'])->name('stream.getdata');
                Route::post('/regenstreamkey', [App\Http\Controllers\IngestStreamController::class, 'regen_stream_key'])->name('stream.regenstreamkey');
                Route::post('/view', [App\Http\Controllers\IngestStreamController::class, 'view_input_stream'])->name('stream.view');
                Route::post('/forcestatus', [App\Http\Controllers\IngestStreamController::class, 'force_status_input_stream'])->name('stream.force_status');
                Route::post('/add', [App\Http\Controllers\IngestStreamController::class, 'add_input_stream'])->name('stream.add');
                Route::post('/edit', [App\Http\Controllers\IngestStreamController::class, 'edit_input_stream'])->name('stream.edit');
                Route::post('/delete', [App\Http\Controllers\IngestStreamController::class, 'delete_input_stream'])->name('stream.delete');
            });
        });

        Route::group(['prefix' => 'output', 'middleware' => 'only.ajax'], function () {
            Route::get('/getdata', [App\Http\Controllers\IngestDestinationController::class, 'get_output_dest'])->name('outputdest.getdata');
            Route::post('/view', [App\Http\Controllers\IngestDestinationController::class, 'view_output_dest'])->name('outputdest.view');
            Route::post('/add', [App\Http\Controllers\IngestDestinationController::class, 'add_output_dest'])->name('outputdest.add');
            Route::post('/edit', [App\Http\Controllers\IngestDestinationController::class, 'edit_output_dest'])->name('outputdest.edit');
            Route::post('/delete', [App\Http\Controllers\IngestDestinationController::class, 'delete_output_dest'])->name('outputdest.delete');
            Route::post('/fetchendpoint', [App\Http\Controllers\IngestDestinationController::class, 'fetch_endpoint_output_dest'])->name('outputdest.fetch_endpoint');
        });

        Route::group(['prefix' => 'fetch', 'middleware' => 'only.ajax'], function () {
            Route::post('/panel/stream_key_encoder', [App\Http\Controllers\PanelController::class, 'get_stream_key'])->name('panel.rtmp_stream_key');
            Route::post('/panel/get_stream_preview', [App\Http\Controllers\PanelController::class, 'check_stream_preview'])->name('panel.rtmp_preview');
            Route::post('/panel/fetch_input_stream', [App\Http\Controllers\PanelController::class, 'fetch_stream_input'])->name('panel.fetch_stream_input');
        });
    });

    Route::group(['middleware' => 'only.ajax'], function () {
        Route::get('/panel/rtmp_stat', [App\Http\Controllers\PanelController::class, 'get_stat_rtmp'])->name('panel.data_rtmp_stat');
    });
});

Route::fallback(function () {
    return abort(404);
});
