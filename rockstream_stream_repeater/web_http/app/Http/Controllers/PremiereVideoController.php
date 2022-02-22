<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;

use App\Models\PremiereVideo;
use App\Models\StreamInput;

use SimpleXMLElement;
use Carbon\CarbonInterval;
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
        return DataTables::of(PremiereVideo::get())
            ->addIndexColumn()
            ->editColumn('video_path', function ($data) {
                return '<button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target=".view-path-video-' . $data->id . '" aria-expanded="false" aria-controls="view-path-video-' . $data->id . '"><span class="material-icons me-1">video_file</span>View Path</button>
                    <div class="collapse view-path-video-' . $data->id . '  my-2">
                            <p>' . htmlspecialchars($data->video_path) . '</p>
                    </div>
                ';
            })
            ->editColumn('is_premiere', function ($data) {
                if ($data->is_premiere == TRUE) {
                    return '<div class="text-success flash-text-item"><span class="material-icons me-1">sensors</span>Premiere</div>';
                } else {
                    return '<div class="text-danger"><span class="material-icons me-1">sensors_off</span>Offline</div>';
                }
            })
            ->editColumn('active_premiere_video', function ($data) {
                if ($data->active_premiere_video == TRUE) {
                    return '<div class="text-success"><span class="material-icons me-1">check_circle</span>Active</div>';
                } else {
                    return '<div class="text-danger"><span class="material-icons me-1">cancel</span>Not Active</div>';
                }
            })
            ->addColumn('actions', function ($data) {
                return '<div class="btn-group">
                    <div class="btn btn-primary view-premiere-video" data-premiere-video-id="' . $data->id . '" data-toggle="tooltip" title="View Premiere Video"><span
                        class="material-icons">description</span></div>
                    <div class="btn btn-secondary start-premiere-video" data-premiere-video-id="' . $data->id . '" data-toggle="tooltip" title="Start Premiere Video"><span
                        class="material-icons">play_arrow</span></div>
                    <div class="btn btn-success edit-premiere-video" data-premiere-video-id="' . $data->id . '" data-toggle="tooltip" title="Edit Premiere Video"><span
                        class="material-icons">edit</span></div>
                    <div class="btn btn-danger delete-premiere-video" data-premiere-video-id="' . $data->id . '" data-toggle="tooltip" title="Delete Premiere Video"><span
                        class="material-icons">delete</span></div>
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
                if (!strstr(mime_content_type($request->path_video), "video/")) {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-danger">This file that you select not a video, Please select correct media.</div>',
                        'success' => FALSE,
                        'isForm' => FALSE
                    ];
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-success">Add premiere video successfully</div>',
                        'success' => TRUE,
                        'isForm' => FALSE
                    ];

                    PremiereVideo::create([
                        'title_video' => $request->name_video,
                        'video_path' => htmlspecialchars($request->path_video),
                        'user_id' => (Auth::check() ? Auth::user()->id : NULL),
                        'is_premiere' => FALSE,
                        'active_premiere_video' => $request->status_video,
                    ]);
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
            if ($check_premiere->user_id == Auth::user()->id) {
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
                    'messages' => '<div class="alert alert-danger"><span class="material-icons me-1">cancel</span>You are not have access to view this output destination</div>'
                ];
            }
        } elseif ($method == "edit") {
            if ($check_premiere->user_id == Auth::user()->id) {
                if ($check_premiere->is_premiere != TRUE) {
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
                            if (!strstr(mime_content_type($request->path_video), "video/")) {
                                $responses = [
                                    'csrftoken' => csrf_token(),
                                    'messages' => '<div class="alert alert-danger">This file that you select not a video, Please select correct media.</div>',
                                    'success' => FALSE,
                                    'isForm' => FALSE
                                ];
                            } else {
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
                                        'user_id' => (Auth::check() ? Auth::user()->id : NULL),
                                        'is_premiere' => FALSE,
                                        'active_premiere_video' => $request->status_video,
                                    ]);
                            }
                        }
                    }
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-danger"><span class="material-icons me-1">block</span>You cannot edit premiere video during premiere.</div>',
                        'is_form' => FALSE,
                        'success' => FALSE
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'is_form' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="material-icons me-1">cancel</span>You are not have access to edit this premiere video</div>'
                ];
            }
        }
        return response()->json($responses);
    }

    public function view_premiere_video(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_premiere = PremiereVideo::where('id', $request->id_premiere_video)->first();
            if ($check_premiere->user_id == Auth::user()->id) {
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
                    'messages' => '<div class="alert alert-danger"><span class="material-icons me-1">cancel</span>You are not have access to view this premiere video</div>'
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
            if ($check_premiere->user_id == Auth::user()->id) {
                if ($method == "show") {
                    if ($check_premiere->user_id == Auth::user()->id) {
                        $data['premiere_video_data'] = $check_premiere;

                        $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => TRUE,
                            'html' => view('layouts.modal_layouts.start_premiere_video', $data)->render()
                        ];
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'messages' => '<div class="alert alert-danger"><span class="material-icons me-1">cancel</span>You are not have access to view this output destination</div>',
                            'success' => FALSE,
                            'isForm' => FALSE
                        ];
                    }
                } elseif ($method == "start") {
                    if ($check_premiere->is_premiere != TRUE) {
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
                                    'bitrate_premiere_video' => 'required|numeric|max:50000'
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
                                    if (!strstr(mime_content_type($check_premiere->video_path), "video/")) {
                                        $responses = [
                                            'csrftoken' => csrf_token(),
                                            'messages' => '<div class="alert alert-danger">This file that you select not a video, Please select correct media.</div>',
                                            'success' => FALSE,
                                            'isForm' => FALSE
                                        ];
                                    } else {
                                        if ($stream_db->is_live == TRUE) {
                                            $responses = [
                                                'csrftoken' => csrf_token(),
                                                'messages' => '<div class="alert alert-danger"><span class="material-icons me-1">block</span>Stop Stream First Before Premiering Video</div>',
                                                'success' => FALSE,
                                                'isForm' => FALSE
                                            ];
                                        } else {
                                            PremiereVideoBroadcast::dispatch(['id' => $check_premiere->id, 'rtmp_output' => (str_replace('http', 'rtmp', config('app.url')) . '/' . $stream_db->name_input_stream . '/' . $stream_db->key_input_stream), 'bitrate_stream' => $request->bitrate_premiere_video])->onQueue('premiere_video_broadcast');

                                            $responses = [
                                                'csrftoken' => csrf_token(),
                                                'messages' => '<div class="alert alert-success">Start premiere video successfully</div>',
                                                'success' => TRUE,
                                                'isForm' => FALSE
                                            ];
                                        }
                                    }
                                }
                            }
                        } else {
                            $responses = [
                                'csrftoken' => csrf_token(),
                                'alert' => '<div class="alert alert-danger">This video premiere is disable, Please enable first to start premiere.</div>',
                                'success' => FALSE,
                                'isForm' => FALSE
                            ];
                        }
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'alert' => '<div class="alert alert-danger">You cannot start premiere video during premiere.</div>',
                            'success' => FALSE,
                            'isForm' => FALSE
                        ];
                    }
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'alert' => '<div class="alert alert-danger">You are not have access to view this premiere video.</div>',
                    'success' => FALSE,
                    'isForm' => FALSE
                ];
            }
        }
        return response()->json($responses);
    }

    public function launch_queue_daemon(Request $request)
    {
        $php_folder = (dirname(base_path()) . DIRECTORY_SEPARATOR . config('component.php_path'));

        if ($request->isMethod('options')) {
            if (!file_exists($php_folder . DIRECTORY_SEPARATOR . 'php.exe')) {
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

                # Check if queue daemon is running
                Utility::runInstancewithPid('start "Premiere Video Stream Daemon" /d"' . $php_folder . '" "php.exe" -f "' . (base_path() . DIRECTORY_SEPARATOR . 'artisan') . '" queue:work --queue premiere_video_broadcast');

                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'alert' => [
                        'icon' => 'success',
                        'title' => 'Launch Daemon Successfully',
                        'text' => 'Launch daemon premiere video successfully',
                    ]
                ];
            }
        }
        return response()->json($responses);
    }
}
