<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        return DataTables::of(StreamInput::with('ingest_destination_data')->get())
            ->addIndexColumn()
            ->addColumn('platform_dest', function ($data) {
                if ($data->ingest_destination_data->count() > 0) {
                    foreach ($data->ingest_destination_data->slice(0, 4) as $key) {
                        $platform_key[] = ($key->platform_dest == 'youtube' ? '<span class="bi bi-youtube me-1"></span>Youtube' : ($key->platform_dest == 'twitch' ? '<span class="bi bi-twitch me-1"></span>Twitch' : ($key->platform_dest == 'custom' ? '<span class="bi bi-diagram-3-fill me-1"></span>Custom' : '<span class="bi bi-question-circle me-1"></span>Unknown')));
                    }
                    return implode(', ', $platform_key) . ($data->ingest_destination_data->count() > 4 ? ', Other...' : '');
                } else {
                    return '-';
                }
            })
            ->addColumn('is_live', function ($data) {
                if ($data->is_live == TRUE) {
                    return '<div class="text-success flash-text-item"><span class="material-icons me-1">sensors</span>Live</div>';
                } else {
                    return '<div class="text-danger"><span class="material-icons me-1">sensors_off</span>Not Live</div';
                }
            })
            ->addColumn('is_active', function ($data) {
                if ($data->active_input_stream == TRUE) {
                    return '<div class="text-success"><span class="material-icons me-1">check_circle</span>Active</div>';
                } else {
                    return '<div class="text-danger"><span class="material-icons me-1">cancel</span>Not Active</div>';
                }
            })
            ->addColumn('actions', function ($data) {
                return '<div class="btn-group">
                    <div class="btn btn-primary regen-input-stream-key" data-input-stream-id="' . $data->identifier_stream . '" data-toggle="tooltip" title="Regenerate Input Stream Key"><span
                        class="material-icons">key</span></div>
                    <a class="btn btn-success" href="' . route('stream.manage', ['id_stream' => $data->identifier_stream]) . '" data-toggle="tooltip" title="Manage Input Stream"><span
                        class="material-icons">edit</span></a>
                    <div class="btn btn-danger delete-input-stream" data-input-stream-id="' . $data->identifier_stream . '" data-toggle="tooltip" title="Delete Input Stream"><span
                        class="material-icons">delete</span></div>
                </div>
            ';
            })
            ->rawColumns(['actions', 'is_live', 'is_active', 'platform_dest'])
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
                'user_id' => (Auth::check() ? Auth::user()->id : NULL),
                'is_live' => FALSE,
                'identifier_stream' => Str::uuid(),
                'name_input_stream' => 'live' . uniqid() . Str::random(10),
                'key_input_stream' => 'key_' . uniqid("", true) . Str::random(15)
            ]);
        }


        return response()->json($responses);
    }

    public function edit_input_stream(Request $request)
    {
        $check_stream = StreamInput::where('identifier_stream', $request->id_input_stream)->first();

        if ($check_stream->user_id == Auth::user()->id) {
            if ($check_stream->is_live != TRUE) {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'messages' => [],
                    'success' => FALSE,
                    'is_form' => TRUE
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
                        'is_form' => FALSE,
                        'success' => TRUE
                    ];

                    StreamInput::where('id', $check_stream['id'])
                        ->update([
                            'name_input' => $request->name_input,
                            'active_input_stream' => $request->status_input
                        ]);
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'messages' => '<div class="alert alert-danger"><span class="material-icons me-1">block</span>Cannot edit input stream because it is live</div>',
                    'is_form' => FALSE,
                    'success' => FALSE
                ];
            }
        } else {
            $responses = [
                'csrftoken' => csrf_token(),
                'success' => FALSE,
                'is_form' => FALSE,
                'messages' => '<div class="alert alert-danger"><span class="material-icons me-1">cancel</span>You are not have access to edit this input stream</div>'
            ];
        }
        return response()->json($responses);
    }

    public function regen_stream_key(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_stream = StreamInput::where('identifier_stream', $request->id_input_stream)->first();

            if ($check_stream) {
                if ($check_stream->user_id == Auth::user()->id) {
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
                            ->update(['key_input_stream' => 'key_' . uniqid("", true) . Str::random(15)]);
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

            if ($check_stream->user_id == Auth::user()->id) {
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
}