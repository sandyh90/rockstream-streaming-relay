<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\Rule;

use App\Models\PremiereVideo;
use App\Models\StreamInput;

use App\Rules\CheckPathFile;

use Yajra\DataTables\DataTables;
use Carbon\Carbon;

use App\Component\Utility;
use App\Component\Facades\Facade\AppInterfacesFacade as AppInterfaces;

use App\Jobs\PremiereVideoBroadcast;
use App\Rules\CheckMimeFile;

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
                            <code>' . htmlspecialchars($data->video_path) . '</code>
                    </div>
                ';
            })
            ->editColumn('is_premiere', function ($data) {
                if ($data->is_premiere == TRUE) {
                    return '<div class="text-success flash-text-item"><span class="bi bi-play-circle me-1"></span>Premiere</div>';
                } else {
                    return '<div class="text-danger"><span class="bi bi-dash-circle me-1"></span>Offline</div>';
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
            'success' => FALSE
        ];

        $validator = Validator::make(
            $request->all(),
            [
                'name_video' => 'required|string|max:100',
                'path_video' => ['required', 'string', 'max:255', new CheckMimeFile(['video/webm', 'video/x-matroska', 'video/avi', 'video/mpeg', 'video/quicktime', 'video/x-msvideo', 'video/mp4']), new CheckPathFile],
                'status_video' => 'required|boolean'
            ]
        );

        if ($validator->fails()) {
            $responses['messages'] = Utility::alertValidation($validator->errors()->all(), 'alert alert-danger');
        } else {
            $responses = [
                'csrftoken' => csrf_token(),
                'messages' => '<div class="alert alert-success">Add premiere video successfully</div>',
                'success' => TRUE
            ];

            PremiereVideo::create([
                'title_video' => $request->name_video,
                'video_path' => htmlspecialchars($request->path_video),
                'user_id' => (Auth::check() ? Auth::id() : NULL),
                'is_premiere' => FALSE,
                'active_premiere_video' => $request->status_video,
            ]);
        }

        return response()->json($responses);
    }

    public function edit_premiere_video(Request $request)
    {
        $method = $request->query('fetch');
        $check_premiere = PremiereVideo::where('id', $request->id_premiere_video)->first();
        if ($method == "show") {
            if ($check_premiere) {
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
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>Premiere video not found</div>'
                ];
            }
        } elseif ($method == "edit") {
            if ($check_premiere) {
                if ($check_premiere->user_id == Auth::id()) {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => [],
                        'success' => FALSE
                    ];

                    $validator = Validator::make(
                        $request->all(),
                        [
                            'name_video' => 'required|string|max:100',
                            'path_video' => ['required', 'string', 'max:255', new CheckMimeFile(['video/webm', 'video/x-matroska', 'video/avi', 'video/mpeg', 'video/quicktime', 'video/x-msvideo', 'video/mp4']), new CheckPathFile],
                            'status_video' => 'required|boolean'
                        ]
                    );

                    if ($validator->fails()) {
                        $responses['messages'] = Utility::alertValidation($validator->errors()->all(), 'alert alert-danger');
                    } else {
                        if ($check_premiere->is_premiere != TRUE) {
                            $responses = [
                                'csrftoken' => csrf_token(),
                                'messages' => '<div class="alert alert-success">Edit premiere video successfully</div>',
                                'success' => TRUE
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
                                'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You cannot edit premiere video during premiere.</div>',
                                'success' => FALSE
                            ];
                        }
                    }
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => FALSE,
                        'messages' => '<div class="alert alert-danger">You are not have access to edit this premiere video</div>'
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger">Premiere video not found</div>'
                ];
            }
        }
        return response()->json($responses);
    }

    public function view_premiere_video(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_premiere = PremiereVideo::where('id', $request->id_premiere_video)->first();
            if ($check_premiere) {
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
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>Premiere video not found</div>'
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
            if ($method == "show") {
                if ($check_premiere) {
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
                            'success' => FALSE
                        ];
                    }
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => FALSE,
                        'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>Premiere video not found</div>'
                    ];
                }
            } elseif ($method == "start") {
                if ($check_premiere) {
                    if ($check_premiere->user_id == Auth::id()) {
                        if ($check_premiere->active_premiere_video == TRUE) {
                            $responses = [
                                'csrftoken' => csrf_token(),
                                'messages' => [],
                                'success' => FALSE
                            ];

                            $validator = Validator::make(
                                $request->all(),
                                [
                                    'rtmp_premiere_input' => 'required|exists:input_stream,identifier_stream',
                                    'bitrate_premiere_video' => 'required|numeric|min:1|max:50000',
                                    'encoder_type_video' => 'required|string|in:libx264,h264_amf,h264_nvenc,h264_qsv',
                                    'video_resolution_size' => 'required|string|in:locked_resolution,follow_resolution,custom_resolution',
                                    'width_custom_resolution' => ($request->video_resolution_size == 'custom_resolution' ? 'required|numeric' : 'nullable'),
                                    'height_custom_resolution' => ($request->video_resolution_size == 'custom_resolution' ? 'required|numeric' : 'nullable'),
                                    'schedule_premiere_video' => 'boolean',
                                    'schedule_datetime_premiere_video' => ($request->schedule_premiere_video ? 'required|date_format:Y-m-d\TH:i' : 'nullable'),
                                    'use_custom_timezone_premiere_schedule' => ($request->schedule_premiere_video ? 'boolean' : 'nullable'),
                                    'custom_timezone_premiere_schedule' => ($request->schedule_premiere_video ? ($request->use_custom_timezone_premiere_schedule ? [
                                        'required',
                                        Rule::in(array_keys(Utility::timezone_gmt_list()))
                                    ] : 'nullable') : 'nullable'),
                                    'countdown_premiere_video' => 'boolean',
                                    'local_video_countdown_path' => ($request->countdown_premiere_video ? ['required', 'string', 'max:255', new CheckMimeFile(['video/webm', 'video/x-matroska', 'video/avi', 'video/mpeg', 'video/quicktime', 'video/x-msvideo', 'video/mp4']), new CheckPathFile] : 'nullable'),
                                ]
                            );

                            if ($validator->fails()) {
                                $responses['messages'] = Utility::alertValidation($validator->errors()->all(), 'alert alert-danger');
                            } else {
                                $stream_db = StreamInput::where(['identifier_stream' => $request->rtmp_premiere_input])->first();
                                if (!file_exists($check_premiere->video_path)) {
                                    $responses = [
                                        'csrftoken' => csrf_token(),
                                        'messages' => '<div class="alert alert-danger">File not found, Please check your file.</div>',
                                        'success' => FALSE
                                    ];
                                } else {
                                    if (Utility::checkFileMime($check_premiere->video_path, ['video/webm', 'video/x-matroska', 'video/avi', 'video/mpeg', 'video/quicktime', 'video/x-msvideo', 'video/mp4'])) {
                                        if ($stream_db->is_live == TRUE) {
                                            $responses = [
                                                'csrftoken' => csrf_token(),
                                                'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You cannot start premiere video during premiere.</div>',
                                                'success' => FALSE
                                            ];
                                        } else {
                                            // Check custom resolution if use 
                                            if ($request->video_resolution_size == 'custom_resolution') {
                                                if (!is_null($request->width_custom_resolution) && !is_null($request->height_custom_resolution)) {
                                                    function getAspectRatio(int $w, int $h)
                                                    {
                                                        function gcd($a, $b)
                                                        {
                                                            return ($b == 0) ? $a : gcd($b, $a % $b);
                                                        }
                                                        $divisor = gcd($w, $h);
                                                        return $w / $divisor . ':' . $h / $divisor;
                                                    }
                                                    $getAspectRatio = getAspectRatio($request->width_custom_resolution, $request->height_custom_resolution);

                                                    if ($getAspectRatio == '0:0' || Str::of($getAspectRatio)->contains('0')) {
                                                        return $responses = [
                                                            'csrftoken' => csrf_token(),
                                                            'messages' => '<div class="alert alert-danger">The resolution custom video is invalid.</div>',
                                                            'success' => FALSE
                                                        ];
                                                    }
                                                } else {
                                                    return $responses = [
                                                        'csrftoken' => csrf_token(),
                                                        'messages' => '<div class="alert alert-danger">The resolution custom video is empty.</div>',
                                                        'success' => FALSE
                                                    ];
                                                }
                                            }
                                            $dispatchPremiere = PremiereVideoBroadcast::dispatch([
                                                'id_video' => $check_premiere->id,
                                                'rtmp_output' => (str_replace('http', 'rtmp', config('app.url')) . '/' . $stream_db->name_input_stream . '/' . $stream_db->key_input_stream),
                                                'bitrate_stream' => $request->bitrate_premiere_video,
                                                'encoder_type' => $request->encoder_type_video,
                                                'type_resolution_size' => $request->video_resolution_size,
                                                'custom_resolution' => [
                                                    'width' => ($request->video_resolution_size == 'custom_resolution' ? (is_null($request->width_custom_resolution) ? NULL : $request->width_custom_resolution) : NULL),
                                                    'height' => ($request->video_resolution_size == 'custom_resolution' ? (is_null($request->height_custom_resolution) ? NULL : $request->height_custom_resolution) : NULL)
                                                ],
                                                'use_countdown' => $request->countdown_premiere_video,
                                                'countdown_video_path' => (is_null($request->local_video_countdown_path) ? NULL : $request->local_video_countdown_path)
                                            ])->onQueue(('PremiereVideoBroadcast_user_id=' . Auth::id()));

                                            // Check if start premiere video with schedule [Beta]

                                            if ($request->schedule_premiere_video == TRUE) {
                                                $parseSchedule = Carbon::parse($request->schedule_datetime_premiere_video);
                                                // check if use custom timezone schedule shift
                                                if ($request->use_custom_timezone_premiere_schedule == TRUE) {
                                                    $parseSchedule->shiftTimezone($request->custom_timezone_premiere_schedule);
                                                }
                                                $dispatchPremiere->delay($parseSchedule);
                                            }

                                            $responses = [
                                                'csrftoken' => csrf_token(),
                                                'messages' => '<div class="alert alert-success">Start premiere video successfully</div>',
                                                'success' => TRUE
                                            ];
                                        }
                                    } else {
                                        $responses = [
                                            'csrftoken' => csrf_token(),
                                            'messages' => '<div class="alert alert-danger">This file that you select not a video, Please select correct media.</div>',
                                            'success' => FALSE
                                        ];
                                    }
                                }
                            }
                        } else {
                            $responses = [
                                'csrftoken' => csrf_token(),
                                'messages' => '<div class="alert alert-danger">This video premiere is disable, Please enable first to start premiere.</div>',
                                'success' => FALSE
                            ];
                        }
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'messages' => '<div class="alert alert-danger">You are not have access to start this premiere video</div>',
                            'success' => FALSE
                        ];
                    }
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-danger">Premiere video not found</div>',
                        'success' => FALSE
                    ];
                }
            }
        }
        return response()->json($responses);
    }

    public function delete_premiere_video(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_premiere = PremiereVideo::where(['id' => $request->id_premiere_video])->first();
            if ($check_premiere) {
                if ($check_premiere->user_id == Auth::id()) {
                    if ($check_premiere->is_premiere != TRUE) {
                        PremiereVideo::where(['id' => $check_premiere->id])->delete();
                        return $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => TRUE,
                            'alert' => [
                                'icon' => 'success',
                                'text' => 'Delete Premiere Video Success'
                            ]
                        ];
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => FALSE,
                            'alert' => [
                                'icon' => 'warning',
                                'text' => 'This Video Are In Premiere'
                            ]
                        ];
                    }
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => FALSE,
                        'alert' => [
                            'icon' => 'warning',
                            'text' => 'You are not have access to delete this premiere video'
                        ]
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'warning',
                        'text' => 'This Premiere Video Not Found'
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
            if ($check_premiere) {
                if ($check_premiere->user_id == Auth::id()) {
                    if ($check_premiere->is_premiere != FALSE) {
                        PremiereVideo::where(['id' => $check_premiere->id])->update(['is_premiere' => FALSE]);
                        return  $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => TRUE,
                            'alert' => [
                                'icon' => 'success',
                                'text' => 'Force Status Premiere Video Success'
                            ]
                        ];
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => FALSE,
                            'alert' => [
                                'icon' => 'warning',
                                'text' => 'This Video Are Not In Premiere State'
                            ]
                        ];
                    }
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => FALSE,
                        'alert' => [
                            'icon' => 'warning',
                            'text' => 'You are not have access to force status this premiere video'
                        ]
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'warning',
                        'text' => 'This Premiere Video Not Found'
                    ]
                ];
            }
        }
        return response()->json($responses);
    }

    public function launch_queue_daemon(Request $request)
    {
        $binaryProc = [
            'phpBinName' => 'php.exe',
            'nginxBinName' => 'nginx.exe',
            'phpPath' => ((AppInterfaces::getsetting('IS_CUSTOM_PHP_BINARY') == TRUE && !empty(AppInterfaces::getsetting('PHP_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('PHP_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('php')),
            'nginxPath' => ((AppInterfaces::getsetting('IS_CUSTOM_NGINX_BINARY') == TRUE && !empty(AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('nginx'))
        ];

        if ($request->isMethod('options')) {
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
                        Utility::runInstancewithPid('start "Premiere Video Stream Daemon ' . (Auth::user()->name ? ('User=' . Auth::user()->name) : ('User_Id=' . Auth::id())) . '" /d"' . $binaryProc['phpPath'] . '" "' . $binaryProc['phpBinName'] . '" -f "' . (base_path() . DIRECTORY_SEPARATOR . 'artisan') . ('" queue:work --queue PremiereVideoBroadcast_user_id=' . Auth::id() . ' --stop-when-empty'));
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => TRUE,
                            'alert' => [
                                'icon' => 'success',
                                'title' => 'Launch Daemon Successfully',
                                'text' => 'Launch daemon premiere video successfully',
                            ]
                        ];
                    } catch (\Throwable $e) {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => FALSE,
                            'alert' => [
                                'icon' => 'warning',
                                'title' => 'Launch Daemon Unsuccessfully',
                                'text' => 'Launch daemon premiere video unsuccessfully, Error: ' . $e->getMessage(),
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
        }
        return response()->json($responses);
    }

    public function get_premiere_queue()
    {
        return DataTables::of(Queue::getDatabase()->table('jobs')->where(['queue' => ('PremiereVideoBroadcast_user_id=' . Auth::id())])->orderBy('created_at', 'asc')->get())
            ->addIndexColumn()
            ->addColumn('queue_name', function ($data) {
                return $data->queue;
            })
            ->editColumn('running_at', function ($data) {
                return $data->reserved_at ? Carbon::createFromTimestamp($data->reserved_at)->format('m-d-Y H:i:s') : '-';
            })
            ->editColumn('schedule_at', function ($data) {
                return $data->available_at ? Carbon::createFromTimestamp($data->available_at)->format('m-d-Y H:i:s') : '-';
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at ? Carbon::createFromTimestamp($data->created_at)->format('m-d-Y H:i:s') : '-';
            })
            ->addColumn('actions', function ($data) {
                return '<div class="btn-group">
                    <div class="btn btn-primary view-premiere-queue" data-premiere-queue-id="' . $data->id . '" data-bs-toggle="tooltip" title="View Premiere Queue"><span
                        class="bi bi-file-earmark-text"></span></div>
                    <div class="btn btn-success edit-premiere-queue" data-premiere-queue-id="' . $data->id . '" data-toggle="tooltip" title="Edit Premiere Queue"><span
                    class="bi bi-pencil-square"></span></div>
                    <div class="btn btn-danger delete-premiere-queue" data-premiere-queue-id="' . $data->id . '" data-bs-toggle="tooltip" title="Delete Premiere Queue"><span
                        class="bi bi-trash"></span></div>
                </div>
            ';
            })
            ->rawColumns(['actions'])
            ->removeColumn(['payload', 'reserved_at', 'available_at', 'attempts', 'queue'])
            ->make(true);
    }

    public function view_premiere_queue(Request $request)
    {
        if ($request->isMethod('post')) {
            $premiereQueueData = Queue::getDatabase()->table('jobs')->where(['queue' => ('PremiereVideoBroadcast_user_id=' . Auth::id()), 'id' => $request->id_premiere_queue])->first();
            if ($premiereQueueData) {
                if ($premiereQueueData->queue == ('PremiereVideoBroadcast_user_id=' . Auth::id())) {
                    $data['premiere_queue_data'] = $premiereQueueData;

                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => TRUE,
                        'html' => view('layouts.modal_layouts.view_premiere_queue', $data)->render()
                    ];
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You are not have access to view this premiere video</div>',
                        'success' => FALSE
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>Premiere queue not found</div>'
                ];
            }
        }
        return response()->json($responses);
    }

    public function edit_premiere_queue(Request $request)
    {
        $method = $request->query('fetch');
        $premiereQueueData = Queue::getDatabase()->table('jobs')->where(['queue' => ('PremiereVideoBroadcast_user_id=' . Auth::id()), 'id' => $request->id_premiere_queue])->first();
        if ($method == "show") {
            if ($premiereQueueData) {
                if ($premiereQueueData->queue == ('PremiereVideoBroadcast_user_id=' . Auth::id())) {
                    $data['premiere_queue_data'] = $premiereQueueData;

                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => TRUE,
                        'html' => view('layouts.modal_layouts.edit_premiere_queue', $data)->render()
                    ];
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You are not have access to edit this premiere video</div>',
                        'success' => FALSE
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>Premiere queue not found</div>'
                ];
            }
        } elseif ($method == "edit") {
            if ($premiereQueueData) {
                if ($premiereQueueData->queue == ('PremiereVideoBroadcast_user_id=' . Auth::id())) {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => [],
                        'success' => FALSE
                    ];

                    $validator = Validator::make(
                        $request->all(),
                        [
                            'schedule_datetime_premiere_video' => 'required|date_format:Y-m-d\TH:i',
                            'use_custom_timezone_premiere_schedule' => 'boolean',
                            'custom_timezone_premiere_schedule' => ($request->use_custom_timezone_premiere_schedule ? [
                                'required',
                                Rule::in(array_keys(Utility::timezone_gmt_list()))
                            ] : 'nullable'),
                        ]
                    );

                    if ($validator->fails()) {
                        $responses['messages'] = Utility::alertValidation($validator->errors()->all(), 'alert alert-danger');
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'messages' => '<div class="alert alert-success">Edit premiere queue successfully</div>',
                            'success' => TRUE
                        ];

                        $parseSchedule = Carbon::parse($request->schedule_datetime_premiere_video);
                        // check if use custom timezone schedule shift
                        if ($request->use_custom_timezone_premiere_schedule == TRUE) {
                            $parseSchedule->shiftTimezone($request->custom_timezone_premiere_schedule);
                        }
                        Queue::getDatabase()->table('jobs')->where(['queue' => ('PremiereVideoBroadcast_user_id=' . Auth::id()), 'id' => $premiereQueueData->id])->update([
                            'available_at' => $parseSchedule->timestamp
                        ]);
                    }
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-danger">You are not have access to edit this premiere queue</div>',
                        'success' => FALSE
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'messages' => '<div class="alert alert-danger">Premiere queue not found</div>',
                    'success' => FALSE
                ];
            }
        }
        return response()->json($responses);
    }

    public function delete_premiere_queue(Request $request)
    {
        if ($request->isMethod('post')) {
            $premiereQueueData = Queue::getDatabase()->table('jobs')->where(['queue' => ('PremiereVideoBroadcast_user_id=' . Auth::id()), 'id' => $request->id_premiere_queue])->first();
            if ($premiereQueueData) {
                if ($premiereQueueData->queue == ('PremiereVideoBroadcast_user_id=' . Auth::id())) {
                    if (!$premiereQueueData->reserved_at || empty($premiereQueueData->reserved_at)) {
                        Queue::getDatabase()->table('jobs')->where(['queue' => ('PremiereVideoBroadcast_user_id=' . Auth::id()), 'id' => $premiereQueueData->id])->delete();
                        return $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => TRUE,
                            'alert' => [
                                'icon' => 'success',
                                'text' => 'Delete Premiere Queue Success',
                            ]
                        ];
                    } else {
                        return $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => TRUE,
                            'alert' => [
                                'icon' => 'warning',
                                'text' => 'Premiere Queue Is Running',
                            ]
                        ];
                    }
                } else {
                    return $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => TRUE,
                        'alert' => [
                            'icon' => 'warning',
                            'text' => 'You are not have access to delete this premiere queue',
                        ]
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'warning',
                        'text' => 'Premiere Queue Not Found',
                    ]
                ];
            }
        }
        return response()->json($responses);
    }
}
