<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

use App\Models\StreamInput;

use Yajra\DataTables\DataTables;

class IngestStreamController extends Controller
{
    //
    public function index()
    {
        return view('stream_input.input_stream');
    }

    public function manage_input_stream($id_stream = NULL)
    {
        $stream_input = StreamInput::where('identifier_stream', $id_stream)->first();
        if ($id_stream != NULL && !empty($stream_input)) {
            return view('stream_input.manage_input_stream', compact('stream_input'));
        } else {
            return redirect()->route('stream.home');
        }
    }

    public function get_input_stream()
    {
        return DataTables::of(StreamInput::with('ingest_destination_data')->where('user_id', Auth::id())->get())
            ->addIndexColumn()
            ->addColumn('name_dest', function ($data) {
                if ($data->ingest_destination_data->count() > 0) {
                    foreach ($data->ingest_destination_data->slice(0, 4) as $key) {
                        $name_dest[] = ($key->active_stream_dest == TRUE ? '<span class="bi bi-toggle-on me-1 text-success"></span>' : '<span class="bi bi-toggle-off me-1 text-danger"></span>') . Str::limit($key->name_stream_dest, 12);
                    }
                    return implode(', ', $name_dest) . ($data->ingest_destination_data->count() > 4 ? ', Other...' : '');
                } else {
                    return '-';
                }
            })
            ->addColumn('is_live', function ($data) {
                if ($data->is_live == TRUE) {
                    return '<div class="text-success flash-text-item"><span class="bi bi-broadcast me-1"></span>Live</div>';
                } else {
                    return '<div class="text-danger"><span class="bi bi-x-circle me-1"></span>Offline</div>';
                }
            })
            ->addColumn('is_active', function ($data) {
                if ($data->active_input_stream == TRUE) {
                    return '<div class="text-success"><span class="bi bi-check-circle me-1"></span>Active</div>';
                } else {
                    return '<div class="text-danger"><span class="bi bi-x-circle me-1"></span>Not Active</div>';
                }
            })
            ->addColumn('actions', function ($data) {
                return '<div class="btn-group">
                    <div class="btn btn-primary view-input-stream" data-input-stream-id="' . $data->identifier_stream . '" data-toggle="tooltip" title="View Input Stream"><span
                        class="bi bi-file-earmark-text"></span></div>
                    <div class="btn btn-secondary regen-input-stream-key" data-input-stream-id="' . $data->identifier_stream . '" data-toggle="tooltip" title="Regenerate Input Stream Key"><span
                        class="bi bi-key"></span></div>
                    <a class="btn btn-success" href="' . route('stream.manage', ['id_stream' => $data->identifier_stream]) . '" data-toggle="tooltip" title="Manage Input Stream"><span
                        class="bi bi-pencil-square"></span></a>
                    <div class="btn btn-warning force-status-input-stream" data-input-stream-id="' . $data->identifier_stream . '" data-bs-toggle="tooltip" title="Force Status Input Stream"><span
                        class="bi bi-toggle-off"></span></div>
                    <div class="btn btn-danger delete-input-stream" data-input-stream-id="' . $data->identifier_stream . '" data-toggle="tooltip" title="Delete Input Stream"><span
                        class="bi bi-trash"></span></div>
                </div>
            ';
            })
            ->rawColumns(['actions', 'is_live', 'is_active', 'name_dest'])
            ->make(true);
    }

    public function add_input_stream(Request $request)
    {
        $responses = [
            'csrftoken' => csrf_token(),
            'messages' => [],
            'success' => FALSE
        ];

        $validator = Validator::make(
            $request->all(),
            [
                'name_input' => 'required|string|max:100',
                'status_input' => 'required|boolean'
            ]
        );

        if ($validator->fails()) {
            $responses['messages'] = $validator->errors()->all();
        } else {
            $responses = [
                'csrftoken' => csrf_token(),
                'messages' => '<div class="alert alert-success">Add input stream successfully</div>',
                'success' => TRUE
            ];

            StreamInput::create([
                'name_input' => $request->name_input,
                'active_input_stream' => $request->status_input,
                'user_id' => (Auth::check() ? Auth::id() : NULL),
                'is_live' => FALSE,
                'identifier_stream' => Str::uuid(),
                'name_input_stream' => 'live_' . uniqid() . Str::random(10),
                'key_input_stream' => 'key-' . Str::uuid() . Str::random(10)
            ]);

            Artisan::call('nginxrtmp:regenconfig');
        }


        return response()->json($responses);
    }

    public function edit_input_stream(Request $request)
    {
        $check_stream = StreamInput::where('identifier_stream', $request->id_input_stream)->first();

        if ($check_stream->user_id == Auth::id()) {
            if ($check_stream->is_live != TRUE) {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'messages' => [],
                    'success' => FALSE,
                    'isForm' => TRUE
                ];

                $validator = Validator::make(
                    $request->all(),
                    [
                        'name_input' => 'required|string|max:100',
                        'status_input' => 'required|boolean'
                    ]
                );

                if ($validator->fails()) {
                    $responses['messages'] = $validator->errors()->all();
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-success">Edit input stream successfully</div>',
                        'isForm' => FALSE,
                        'success' => TRUE
                    ];

                    StreamInput::where('id', $check_stream->id)
                        ->update([
                            'name_input' => $request->name_input,
                            'active_input_stream' => $request->status_input
                        ]);

                    if ($check_stream->active_input_stream != $request->status_input) {
                        Artisan::call('nginxrtmp:regenconfig');
                    }
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>Cannot edit input stream because it is live</div>',
                    'isForm' => FALSE,
                    'success' => FALSE
                ];
            }
        } else {
            $responses = [
                'csrftoken' => csrf_token(),
                'success' => FALSE,
                'isForm' => FALSE,
                'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You are not have access to edit this input stream</div>'
            ];
        }
        return response()->json($responses);
    }

    public function view_input_stream(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_stream = StreamInput::where('identifier_stream', $request->id_input_stream)->first();
            if ($check_stream->user_id == Auth::id()) {
                $data['input_stream_data'] = $check_stream;

                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'html' => view('layouts.modal_layouts.view_input_stream', $data)->render()
                ];
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You are not have access to view this output destination</div>'
                ];
            }
        }
        return response()->json($responses);
    }

    public function regen_stream_key(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_stream = StreamInput::where('identifier_stream', $request->id_input_stream)->first();

            if ($check_stream) {
                if ($check_stream->user_id == Auth::id()) {
                    if ($check_stream->is_live != TRUE) {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => TRUE,
                            'alert' => [
                                'icon' => 'success',
                                'title' => 'Regenerate Stream Key Success'
                            ]
                        ];

                        StreamInput::where('id', $check_stream['id'])
                            ->update(['key_input_stream' => 'key-' . Str::uuid() . Str::random(10)]);

                        Artisan::call('nginxrtmp:regenconfig');
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'success' => FALSE,
                            'alert' => [
                                'icon' => 'warning',
                                'title' => 'This Input Stream Are In Live'
                            ]
                        ];
                    }
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => FALSE,
                        'alert' => [
                            'icon' => 'warning',
                            'title' => 'You are not have access to regenerate key this input stream'
                        ]
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'alert' => [
                        'icon' => 'warning',
                        'title' => 'Regenerate Stream Key Failed'
                    ]
                ];
            }
        }
        return response()->json($responses);
    }

    public function delete_input_stream(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_stream = StreamInput::where('identifier_stream', $request->id_input_stream)->first();

            if ($check_stream->user_id == Auth::id()) {
                if ($check_stream->is_live != TRUE) {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => TRUE,
                        'alert' => [
                            'icon' => 'success',
                            'title' => 'Delete Input Stream Success'
                        ]
                    ];

                    StreamInput::where('id', $check_stream['id'])
                        ->delete();

                    Artisan::call('nginxrtmp:regenconfig');
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => FALSE,
                        'alert' => [
                            'icon' => 'warning',
                            'title' => 'This Input Stream Are In Live'
                        ]
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'warning',
                        'title' => 'You are not have access to delete this input stream'
                    ]
                ];
            }
        }
        return response()->json($responses);
    }

    public function force_status_input_stream(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_stream = StreamInput::where('identifier_stream', $request->id_input_stream)->first();

            if ($check_stream->user_id == Auth::id()) {
                if ($check_stream->is_live != FALSE) {
                    StreamInput::where(['id' => $check_stream->id])->update(['is_live' => FALSE]);
                    return  $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => TRUE,
                        'alert' => [
                            'icon' => 'success',
                            'title' => 'Force Status Input Stream Success'
                        ]
                    ];
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => FALSE,
                        'alert' => [
                            'icon' => 'warning',
                            'title' => 'This Input Are Not In Live State'
                        ]
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'warning',
                        'title' => 'You are not have access to force status this input stream'
                    ]
                ];
            }
        }
        return response()->json($responses);
    }
}
