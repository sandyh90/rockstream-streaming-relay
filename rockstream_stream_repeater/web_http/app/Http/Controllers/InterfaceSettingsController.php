<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Jobs\TestStreamingBroadcast;

use App\Component\Utility;

use App\Models\StreamInput;

class InterfaceSettingsController extends Controller
{
    public function index()
    {
        return view('interface_settings');
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
                            ])->onQueue('test_streaming_broadcast');

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
        $php_folder = Utility::defaultBinDirFolder(config('component.php_path'));

        if ($request->isMethod('options')) {
            if (Auth::user()->is_operator == TRUE) {
                if (!file_exists($php_folder . DIRECTORY_SEPARATOR . 'php.rockstream.exe')) {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => FALSE,
                        'alert' => [
                            'icon' => 'warning',
                            'title' => 'PHP binaries not found',
                            'text' => 'PHP binaries not found, Please check your PHP binaries path.'
                        ]
                    ];
                } else {
                    if (Utility::getInstanceRunByPath((Utility::defaultBinDirFolder(config('component.nginx_path')) . DIRECTORY_SEPARATOR . 'nginx.exe'))) {
                        # Check if queue daemon is running
                        Utility::runInstancewithPid('start "Test Streaming Daemon" /d"' . $php_folder . '" "php.rockstream.exe" -f "' . (base_path() . DIRECTORY_SEPARATOR . 'artisan') . '" queue:work --queue test_streaming_broadcast --stop-when-empty');

                        $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => TRUE,
                            'alert' => [
                                'icon' => 'success',
                                'title' => 'Launch Daemon Successfully',
                                'text' => 'Launch daemon test streaming successfully',
                            ]
                        ];
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
