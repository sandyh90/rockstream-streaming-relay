<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\PremiereVideo;
use App\Models\StreamInput;

use Yajra\DataTables\DataTables;

use App\Component\Utility;

use App\Jobs\PremiereVideoBroadcast;

class PremiereVideoController extends Controller
{
    //
    public function index()
    {
        return view('premiere_video');
    }

    public function get_premiere_video()
    {
        return DataTables::of(PremiereVideo::where('user_id', Auth::id())->get())
            ->addIndexColumn()
            ->editColumn('video_path', function ($data) {
                return '<button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target=".view-path-video-' . $data->id . '" aria-expanded="false" aria-controls="view-path-video-' . $data->id . '"><span class="bi bi-file-earmark-play me-1"></span>View Path</button>
                    <div class="collapse view-path-video-' . $data->id . '  my-2">
                            <p>' . htmlspecialchars($data->video_path) . '</p>
                    </div>
                ';
            })
            ->editColumn('is_premiere', function ($data) {
                if ($data->is_premiere == TRUE) {
                    return '<div class="text-success flash-text-item"><span class="bi bi-play-circle me-1"></span>Premiere</div>';
                } else {
                    return '<div class="text-danger"><span class="bi bi-x-circle me-1"></span>Offline</div>';
                }
            })
            ->editColumn('active_premiere_video', function ($data) {
                if ($data->active_premiere_video == TRUE) {
                    return '<div class="text-success"><span class="bi bi-check-circle me-1"></span>Active</div>';
                } else {
                    return '<div class="text-danger"><span class="bi bi-x-circle me-1"></span>Not Active</div>';
                }
            })
            ->addColumn('actions', function ($data) {
                return '<div class="btn-group">
                    <div class="btn btn-primary view-premiere-video" data-premiere-video-id="' . $data->id . '" data-bs-toggle="tooltip" title="View Premiere Video"><span
                        class="bi bi-file-earmark-text"></span></div>
                    <div class="btn btn-secondary start-premiere-video" data-premiere-video-id="' . $data->id . '" data-bs-toggle="tooltip" title="Start Premiere Video"><span
                        class="bi bi-play"></span></div>
                    <div class="btn btn-success edit-premiere-video" data-premiere-video-id="' . $data->id . '" data-bs-toggle="tooltip" title="Edit Premiere Video"><span
                        class="bi bi-pencil-square"></span></div>
                    <div class="btn btn-warning force-status-premiere-video" data-premiere-video-id="' . $data->id . '" data-bs-toggle="tooltip" title="Force Status Premiere Video"><span
                        class="bi bi-toggle-off"></span></div>
                    <div class="btn btn-danger delete-premiere-video" data-premiere-video-id="' . $data->id . '" data-bs-toggle="tooltip" title="Delete Premiere Video"><span
                        class="bi bi-trash"></span></div>
                </div>
            ';
            })
            ->rawColumns(['actions', 'is_premiere', 'active_premiere_video', 'video_path'])
            ->make(true);
    }

    public function add_premiere_video(Request $request)
    {
        $responses = [
            'csrftoken' => csrf_token(),
            'messages' => [],
            'success' => FALSE,
            'isForm' => TRUE
        ];

        $validator = Validator::make(
            $request->all(),
            [
                'name_video' => 'required|string|max:100',
                'path_video' => 'required|string|max:255',
                'status_video' => 'required|boolean'
            ]
        );

        if ($validator->fails()) {
            $responses['messages'] = $validator->errors()->all();
        } else {
            if (!file_exists($request->path_video)) {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'messages' => '<div class="alert alert-danger">File not found, Please check your file.</div>',
                    'success' => FALSE,
                    'isForm' => FALSE
                ];
            } else {
                if (Utility::checkFileMime($request->path_video, ['video/mp4', 'video/webm', 'video/x-msvideo'])) {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-success">Add premiere video successfully</div>',
                        'success' => TRUE,
                        'isForm' => FALSE
                    ];

                    PremiereVideo::create([
                        'title_video' => $request->name_video,
                        'video_path' => htmlspecialchars($request->path_video),
                        'user_id' => (Auth::check() ? Auth::id() : NULL),
                        'is_premiere' => FALSE,
                        'active_premiere_video' => $request->status_video,
                    ]);
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-danger">This file that you select not a video, Please select correct media.</div>',
                        'success' => FALSE,
                        'isForm' => FALSE
                    ];
                }
            }
        }

        return response()->json($responses);
    }

    public function edit_premiere_video(Request $request)
    {
        $method = $request->query('fetch');
        $check_premiere = PremiereVideo::where('id', $request->id_premiere_video)->first();
        if ($method == "show") {
            if ($check_premiere->user_id == Auth::id()) {
                $data['premiere_video_data'] = $check_premiere;

                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'html' => view('layouts.modal_layouts.edit_premiere_video', $data)->render()
                ];
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You are not have access to view this premiere video</div>'
                ];
            }
        } elseif ($method == "edit") {
            if ($check_premiere->user_id == Auth::id()) {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'messages' => [],
                    'success' => FALSE,
                    'isForm' => TRUE
                ];

                $validator = Validator::make(
                    $request->all(),
                    [
                        'name_video' => 'required|string|max:100',
                        'path_video' => 'required|string|max:255',
                        'status_video' => 'required|boolean'
                    ]
                );

                if ($validator->fails()) {
                    $responses['messages'] = $validator->errors()->all();
                } else {
                    if ($check_premiere->is_premiere != TRUE) {
                        if (!file_exists($request->path_video)) {
                            $responses = [
                                'csrftoken' => csrf_token(),
                                'messages' => '<div class="alert alert-danger">File not found, Please check your file.</div>',
                                'success' => FALSE,
                                'isForm' => FALSE
                            ];
                        } else {
                            if (Utility::checkFileMime($request->path_video, ['video/mp4', 'video/webm', 'video/x-msvideo'])) {
                                $responses = [
                                    'csrftoken' => csrf_token(),
                                    'messages' => '<div class="alert alert-success">Edit premiere video successfully</div>',
                                    'success' => TRUE,
                                    'isForm' => FALSE
                                ];

                                PremiereVideo::where('id', $check_premiere['id'])
                                    ->update([
                                        'title_video' => $request->name_video,
                                        'video_path' => htmlspecialchars($request->path_video),
                                        'user_id' => (Auth::check() ? Auth::id() : NULL),
                                        'is_premiere' => FALSE,
                                        'active_premiere_video' => $request->status_video,
                                    ]);
                            } else {
                                $responses = [
                                    'csrftoken' => csrf_token(),
                                    'messages' => '<div class="alert alert-danger">This file that you select not a video, Please select correct media.</div>',
                                    'success' => FALSE,
                                    'isForm' => FALSE
                                ];
                            }
                        }
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You cannot edit premiere video during premiere.</div>',
                            'is_form' => FALSE,
                            'success' => FALSE
                        ];
                    }
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'is_form' => FALSE,
                    'messages' => '<div class="alert alert-danger">You are not have access to edit this premiere video</div>'
                ];
            }
        }
        return response()->json($responses);
    }

    public function view_premiere_video(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_premiere = PremiereVideo::where('id', $request->id_premiere_video)->first();
            if ($check_premiere->user_id == Auth::id()) {
                $data['premiere_video_data'] = $check_premiere;

                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'html' => view('layouts.modal_layouts.view_premiere_video', $data)->render()
                ];
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You are not have access to view this premiere video</div>'
                ];
            }
        }
        return response()->json($responses);
    }

    public function start_premiere_video(Request $request)
    {
        $method = $request->query('fetch');
        if ($request->isMethod('post')) {
            $check_premiere = PremiereVideo::where('id', $request->id_premiere_video)->first();
            if ($check_premiere->user_id == Auth::id()) {
                if ($method == "show") {
                    if ($check_premiere->user_id == Auth::id()) {
                        $data['premiere_video_data'] = $check_premiere;

                        $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => TRUE,
                            'html' => view('layouts.modal_layouts.start_premiere_video', $data)->render()
                        ];
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You are not have access to view this premiere video</div>',
                            'success' => FALSE,
                            'isForm' => FALSE
                        ];
                    }
                } elseif ($method == "start") {
                    if ($check_premiere->active_premiere_video == TRUE) {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'messages' => [],
                            'success' => FALSE,
                            'isForm' => TRUE
                        ];

                        $validator = Validator::make(
                            $request->all(),
                            [
                                'rtmp_premiere_input' => 'required|exists:input_stream,identifier_stream',
                                'bitrate_premiere_video' => 'required|numeric|min:1|max:50000',
                                'encoder_type_video' => 'required|string|in:libx264,h264_amf,h264_nvenc,h264_qsv',
                                'countdown_premiere_video' => 'boolean',
                                'use_local_video_countdown' => ($request->countdown_premiere_video ? 'boolean' : 'nullable'),
                                'local_video_countdown_path' => ($request->use_local_video_countdown ? 'required|string|max:255' : 'nullable'),
                            ]
                        );

                        if ($validator->fails()) {
                            $responses['messages'] = $validator->errors()->all();
                        } else {
                            $stream_db = StreamInput::where(['identifier_stream' => $request->rtmp_premiere_input])->first();
                            if (!file_exists($check_premiere->video_path)) {
                                $responses = [
                                    'csrftoken' => csrf_token(),
                                    'messages' => '<div class="alert alert-danger">File not found, Please check your file.</div>',
                                    'success' => FALSE,
                                    'isForm' => FALSE
                                ];
                            } else {
                                if (Utility::checkFileMime($check_premiere->video_path, ['video/mp4', 'video/webm', 'video/x-msvideo'])) {
                                    if ($stream_db->is_live == TRUE) {
                                        $responses = [
                                            'csrftoken' => csrf_token(),
                                            'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>Stop Stream First Before Premiering Video</div>',
                                            'success' => FALSE,
                                            'isForm' => FALSE
                                        ];
                                    } else {

                                        // Check file mime type custom countdown video file if use local countdown video
                                        if ((!Utility::checkFileMime($request->local_video_countdown_path, ['video/mp4', 'video/webm', 'video/x-msvideo']) && $request->use_local_video_countdown == TRUE)) {
                                            return $responses = [
                                                'csrftoken' => csrf_token(),
                                                'messages' => '<div class="alert alert-danger">This file custom countdown that you select not a video, Please select correct media.</div>',
                                                'success' => FALSE,
                                                'isForm' => FALSE
                                            ];
                                        }
                                        PremiereVideoBroadcast::dispatch([
                                            'id_video' => $check_premiere->id,
                                            'rtmp_output' => (str_replace('http', 'rtmp', config('app.url')) . '/' . $stream_db->name_input_stream . '/' . $stream_db->key_input_stream),
                                            'bitrate_stream' => $request->bitrate_premiere_video,
                                            'encoder_type' => $request->encoder_type_video,
                                            'use_countdown' => $request->countdown_premiere_video,
                                            'custom_countdown' => [
                                                'use_custom' => (is_null($request->use_local_video_countdown) ? FALSE : $request->use_local_video_countdown),
                                                'custom_countdown_video_path' => (is_null($request->local_video_countdown_path) ? NULL : $request->local_video_countdown_path)
                                            ]
                                        ])->onQueue('premiere_video_broadcast');

                                        $responses = [
                                            'csrftoken' => csrf_token(),
                                            'messages' => '<div class="alert alert-success">Start premiere video successfully</div>',
                                            'success' => TRUE,
                                            'isForm' => FALSE
                                        ];
                                    }
                                } else {
                                    $responses = [
                                        'csrftoken' => csrf_token(),
                                        'messages' => '<div class="alert alert-danger">This file that you select not a video, Please select correct media.</div>',
                                        'success' => FALSE,
                                        'isForm' => FALSE
                                    ];
                                }
                            }
                        }
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'messages' => '<div class="alert alert-danger">This video premiere is disable, Please enable first to start premiere.</div>',
                            'success' => FALSE,
                            'isForm' => FALSE
                        ];
                    }
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'messages' => '<div class="alert alert-danger">You are not have access to this premiere video.</div>',
                    'success' => FALSE,
                    'isForm' => FALSE
                ];
            }
        }
        return response()->json($responses);
    }

    public function delete_premiere_video(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_premiere = PremiereVideo::where(['id' => $request->id_premiere_video])->first();
            if ($check_premiere->user_id == Auth::id()) {
                if ($check_premiere->is_premiere != TRUE) {
                    PremiereVideo::where(['id' => $check_premiere->id])->delete();
                    return  $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => TRUE,
                        'alert' => [
                            'icon' => 'success',
                            'title' => 'Delete Premiere Video Success'
                        ]
                    ];
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => FALSE,
                        'alert' => [
                            'icon' => 'warning',
                            'title' => 'This Video Are In Premiere'
                        ]
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'warning',
                        'title' => 'You are not have access to delete this premiere video'
                    ]
                ];
            }
        }
        return response()->json($responses);
    }

    public function force_status_premiere_video(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_premiere = PremiereVideo::where(['id' => $request->id_premiere_video])->first();
            if ($check_premiere->user_id == Auth::id()) {
                if ($check_premiere->is_premiere != FALSE) {
                    PremiereVideo::where(['id' => $check_premiere->id])->update(['is_premiere' => FALSE]);
                    return  $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => TRUE,
                        'alert' => [
                            'icon' => 'success',
                            'title' => 'Force Status Premiere Video Success'
                        ]
                    ];
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => FALSE,
                        'alert' => [
                            'icon' => 'warning',
                            'title' => 'This Video Are Not In Premiere State'
                        ]
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'warning',
                        'title' => 'You are not have access to force status this premiere video'
                    ]
                ];
            }
        }
        return response()->json($responses);
    }

    public function launch_queue_daemon(Request $request)
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
                        Utility::runInstancewithPid('start "Premiere Video Stream Daemon" /d"' . $php_folder . '" "php.rockstream.exe" -f "' . (base_path() . DIRECTORY_SEPARATOR . 'artisan') . '" queue:work --queue premiere_video_broadcast');

                        $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => TRUE,
                            'alert' => [
                                'icon' => 'success',
                                'title' => 'Launch Daemon Successfully',
                                'text' => 'Launch daemon premiere video successfully',
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
