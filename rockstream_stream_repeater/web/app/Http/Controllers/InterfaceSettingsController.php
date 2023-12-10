<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Jobs\TestStreamingBroadcast;

use App\Component\Utility;
use App\Component\Facades\Facade\AppInterfacesFacade as AppInterfaces;

use App\Models\StreamInput;

use App\Component\Addons\CachedValuestore;
use App\Rules\CheckPathFile;

class InterfaceSettingsController extends Controller
{
    public function index()
    {
        $data = ['getSettingConfig' => CachedValuestore::make(storage_path('app/settings-app.json'))->all()];
        return view('interface_settings', $data);
    }

    public function reset_factory(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_stream = StreamInput::where('is_live', TRUE);
            if ($check_stream->count() > 0) {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'warning',
                        'title' => 'There\'s Have Input Stream Are In Live'
                    ]
                ];
            } else {

                // Erase all data in database and reset database
                Artisan::call('migrate:fresh');

                // Regenerate nginx config file and restart nginx service to apply new config
                Artisan::call('nginxrtmp:regenconfig');

                // Delete settings-app.json file
                if (file_exists(storage_path('app/settings-app.json'))) {
                    unlink(storage_path('app/settings-app.json'));
                }

                $binaryProc = [
                    'nginxBinName' => 'nginx.exe',
                    'nginxPath' => ((AppInterfaces::getsetting('IS_CUSTOM_NGINX_BINARY') == TRUE && !empty(AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('nginx'))
                ];

                // Stop nginx service if it's running
                if (file_exists($binaryProc['nginxPath'] . DIRECTORY_SEPARATOR . $binaryProc['nginxBinName']) && (Utility::getInstanceRunByPath($binaryProc['nginxPath'] . DIRECTORY_SEPARATOR . $binaryProc['nginxBinName'], $binaryProc['nginxBinName'])['found_process'] == true)) {
                    try {
                        Utility::runInstancewithPid('cmd /c start /B "" /d"' . $binaryProc['nginxPath'] . '" "' . $binaryProc['nginxBinName'] . '" -s stop');
                    } catch (\Throwable $e) {
                    }
                }

                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'success',
                        'title' => 'Factory Reset Success'
                    ]
                ];
            }
        }
        return response()->json($responses);
    }

    public function edit_app_settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $responses = [
                'csrftoken' => csrf_token(),
                'messages' => [],
                'success' => FALSE,
                'isForm' => TRUE
            ];

            if (Auth::user()->is_operator == TRUE) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'use_live_preview' => 'boolean',
                        'enable_custom_php_path' => 'boolean',
                        'enable_custom_ffmpeg_path' => 'boolean',
                        'enable_custom_ffprobe_path' => 'boolean',
                        'enable_custom_nginx_path' => 'boolean',
                        'php_custom_dir' => ($request->php_custom_dir ? ['required', 'string', 'max:255', new CheckPathFile] : 'nullable'),
                        'ffmpeg_custom_dir' => ($request->ffmpeg_custom_dir ? ['required', 'string', 'max:255', new CheckPathFile] : 'nullable'),
                        'ffprobe_custom_dir' => ($request->ffprobe_custom_dir ? ['required', 'string', 'max:255', new CheckPathFile] : 'nullable'),
                        'nginx_custom_dir' => ($request->nginx_custom_dir ? ['required', 'string', 'max:255', new CheckPathFile] : 'nullable'),
                        'reload_system_switch' => 'boolean',
                    ]
                );


                if ($validator->fails()) {
                    $responses['messages'] = $validator->errors()->all();
                } else {
                    CachedValuestore::make(storage_path('app/settings-app.json'))->put([
                        'USE_LIVE_PREVIEW' => $request->boolean('use_live_preview'),
                        'IS_CUSTOM_PHP_BINARY' => $request->boolean('enable_custom_php_path'),
                        'IS_CUSTOM_FFMPEG_BINARY' => $request->boolean('enable_custom_ffmpeg_path'),
                        'IS_CUSTOM_FFPROBE_BINARY' => $request->boolean('enable_custom_ffprobe_path'),
                        'IS_CUSTOM_NGINX_BINARY' => $request->boolean('enable_custom_nginx_path'),
                        'PHP_BINARY_DIRECTORY' => $request->php_custom_dir,
                        'FFMPEG_BINARY_DIRECTORY' => $request->ffmpeg_custom_dir,
                        'FFPROBE_BINARY_DIRECTORY' => $request->ffprobe_custom_dir,
                        'NGINX_BINARY_DIRECTORY' => $request->nginx_custom_dir
                    ]);

                    if (($request->boolean('reload_system_switch') == TRUE) || (AppInterfaces::getsetting('RELOAD_SYSTEM_SWITCH') == TRUE)) {
                        // Regenerate nginx config file and restart nginx service to apply new config
                        Artisan::call('nginxrtmp:regenconfig');
                    }

                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-success">Editing app settings successfuly</div>',
                        'success' => TRUE,
                        'isForm' => FALSE,
                        'test' => AppInterfaces::getsetting('USE_LIVE_PREVIEW')
                    ];
                }
            }
        } else {
            $responses = [
                'csrftoken' => csrf_token(),
                'messages' => '<div class="alert alert-danger">You are not have access to edit this app settings.</div>',
                'success' => FALSE,
                'isForm' => FALSE
            ];
        }
        return response()->json($responses);
    }

    public function start_testing_streaming(Request $request)
    {
        $method = $request->query('fetch');
        if ($request->isMethod('post')) {
            if ($method == "show") {
                if (Auth::user()->is_operator == TRUE) {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => TRUE,
                        'html' => view('layouts.modal_layouts.start_test_streaming')->render()
                    ];
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You are not have access to view this streaming test</div>',
                        'success' => FALSE,
                        'isForm' => FALSE
                    ];
                }
            } elseif ($method == "start") {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'messages' => [],
                    'success' => FALSE,
                    'isForm' => TRUE
                ];

                if (Auth::user()->is_operator == TRUE) {
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'rtmp_test_input' => 'required|exists:input_stream,identifier_stream',
                            'bitrate_test_video' => 'required|numeric|min:1|max:50000',
                            'encoder_type_video' => 'required|string|in:libx264,h264_amf,h264_nvenc,h264_qsv',
                            'fps_type_video' => 'required|string|in:30,40,50,60',
                            'limit_duration_stream' => 'boolean',
                        ]
                    );

                    if ($validator->fails()) {
                        $responses['messages'] = $validator->errors()->all();
                    } else {
                        $stream_db = StreamInput::where(['identifier_stream' => $request->rtmp_test_input])->first();
                        if ($stream_db->is_live == TRUE) {
                            $responses = [
                                'csrftoken' => csrf_token(),
                                'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>Stop Stream First Before Start Test Streaming</div>',
                                'success' => FALSE,
                                'isForm' => FALSE
                            ];
                        } else {
                            TestStreamingBroadcast::dispatch([
                                'rtmp_output' => (str_replace('http', 'rtmp', config('app.url')) . '/' . $stream_db->name_input_stream . '/' . $stream_db->key_input_stream),
                                'bitrate_stream' => $request->bitrate_test_video,
                                'encoder_type' => $request->encoder_type_video,
                                'fps_type' => $request->fps_type_video,
                                'limit_duration' => $request->limit_duration_stream,
                            ])->onQueue('TestStreamingBroadcast');

                            $responses = [
                                'csrftoken' => csrf_token(),
                                'messages' => '<div class="alert alert-success">Start streaming test successfully</div>',
                                'success' => TRUE,
                                'isForm' => FALSE
                            ];
                        }
                    }
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'messages' => '<div class="alert alert-danger">You are not have access to this streaming test.</div>',
                    'success' => FALSE,
                    'isForm' => FALSE
                ];
            }
        }
        return response()->json($responses);
    }

    public function launch_test_streaming_daemon(Request $request)
    {
        $binaryProc = [
            'nginxBinName' => 'nginx.exe',
            'phpBinName' => 'php.exe',
            'phpPath' => ((AppInterfaces::getsetting('IS_CUSTOM_PHP_BINARY') == TRUE && !empty(AppInterfaces::getsetting('PHP_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('PHP_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('php')),
            'nginxPath' => ((AppInterfaces::getsetting('IS_CUSTOM_NGINX_BINARY') == TRUE && !empty(AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('nginx'))
        ];

        if ($request->isMethod('options')) {
            if (Auth::user()->is_operator == TRUE) {
                if (!file_exists($binaryProc['phpPath'] . DIRECTORY_SEPARATOR . $binaryProc['phpBinName'])) {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => FALSE,
                        'alert' => [
                            'icon' => 'warning',
                            'title' => 'PHP binaries not found',
                            'text' => 'PHP binaries not found, Please check your PHP binaries path and rename it to "php.rockstream".'
                        ]
                    ];
                } else {
                    # Check if nginx is running
                    if (Utility::getInstanceRunByPath($binaryProc['nginxPath'] . DIRECTORY_SEPARATOR . $binaryProc['nginxBinName'], $binaryProc['nginxBinName'])['found_process']) {

                        try {
                            Utility::runInstancewithPid('start "Test Streaming Daemon" /d"' . $binaryProc['phpPath'] . '" "' . $binaryProc['phpBinName'] . '" -f "' . (base_path() . DIRECTORY_SEPARATOR . 'artisan') . '" queue:work --queue TestStreamingBroadcast --stop-when-empty');
                            $responses = [
                                'csrftoken' => csrf_token(),
                                'success' => TRUE,
                                'alert' => [
                                    'icon' => 'success',
                                    'title' => 'Launch Daemon Successfully',
                                    'text' => 'Launch daemon test streaming successfully',
                                ]
                            ];
                        } catch (\Throwable $e) {
                            $responses = [
                                'csrftoken' => csrf_token(),
                                'success' => FALSE,
                                'alert' => [
                                    'icon' => 'warning',
                                    'title' => 'Launch Daemon Unsuccessfully',
                                    'text' => 'Launch daemon test streaming unsuccessfully, Error: ' . $e->getMessage(),
                                ]
                            ];
                        }
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => FALSE,
                            'alert' => [
                                'icon' => 'warning',
                                'title' => 'Nginx service not running',
                                'text' => 'Nginx service not running, Please turn on nginx service first.'
                            ]
                        ];
                    }
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'warning',
                        'title' => 'Access Denied',
                        'text' => 'You are not have access to launch queue daemon, Contact your administrator.'
                    ]
                ];
            }
        }
        return response()->json($responses);
    }
}
