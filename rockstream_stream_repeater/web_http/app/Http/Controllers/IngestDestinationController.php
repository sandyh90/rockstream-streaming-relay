<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\IngestStreamDestination;
use App\Models\StreamInput;

use Yajra\DataTables\DataTables;

class IngestDestinationController extends Controller
{
    public function get_output_dest(Request $request)
    {
        return DataTables::of(IngestStreamDestination::with('input_stream_data')->whereHas('input_stream_data', function ($query) use ($request) {
            return $query->where('identifier_stream', $request->id_input_stream);
        })->get())
            ->addIndexColumn()
            ->editColumn('platform_dest', function ($data) {
                return ($data->platform_dest == 'youtube' ? '<span class="bi bi-youtube me-1"></span>Youtube' : ($data->platform_dest == 'twitch' ? '<span class="bi bi-twitch me-1"></span>Twitch' : ($data->platform_dest == 'custom' ? '<span class="bi bi-diagram-3-fill me-1"></span>Custom' : '<span class="bi bi-question-circle me-1"></span>Unknown')));
            })
            ->addColumn('is_active', function ($data) {
                if ($data->active_stream_dest == TRUE) {
                    return '<div class="text-success"><span class="material-icons me-1">check_circle</span>Active</div>';
                } else {
                    return '<div class="text-danger"><span class="material-icons me-1">cancel</span>Not Active</div>';
                }
            })
            ->addColumn('actions', function ($data) {
                if ($data->input_stream_data->is_live != TRUE) {
                    return '<div class="btn-group">
                <div class="btn btn-primary view-output-dest" data-dest-output-id="' . $data->id . '" data-toggle="tooltip" title="View Output Destination"><span
                    class="material-icons">description</span></div>
                <div class="btn btn-success edit-output-dest" data-dest-output-id="' . $data->id . '" data-toggle="tooltip" title="Edit Output Destination"><span
                    class="material-icons">edit</span></div>
                <div class="btn btn-danger delete-output-dest" data-dest-output-id="' . $data->id . '" data-toggle="tooltip" title="Delete Output Destination"><span
                    class="material-icons">delete</span></div>
                </div>
                ';
                } else {
                    return '<div class="text-danger"><span class="material-icons me-1">block</span>Stream Active</div>';
                };
            })
            ->rawColumns(['actions', 'is_active', 'platform_dest'])
            ->make(true);
    }

    public function add_output_dest(Request $request)
    {
        $check_stream = StreamInput::with('ingest_destination_data')->where('id', $request->input_stream_id)->first();
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
                    'name_output_dest' => 'required|string|max:100',
                    'platform_output_dest' => 'required|in:youtube,twitch,custom',
                    'rtmp_output_server' => 'required|url|string|max:255|starts_with:rtmp://',
                    'rtmp_stream_key' => empty($check_stream->ingest_destination_data()->first()->key_stream_dest) ? 'required|string|max:255' : ($check_stream->ingest_destination_data()->first()->key_stream_dest != $request->rtmp_stream_key ? 'required|string|max:255' : 'required|unique:stream_ingest_dest,key_stream_dest|string|max:255'),
                    'status_output_dest' => 'required|boolean'
                ]
            );

            if ($validator->fails()) {
                $responses['messages'] = $validator->errors()->all();
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'messages' => '<div class="alert alert-success">Add output destination successfully</div>',
                    'success' => TRUE
                ];

                IngestStreamDestination::create([
                    'input_stream_id' => $request->input_stream_id,
                    'user_id' => (Auth::check() ? Auth::user()->id : NULL),
                    'name_stream_dest' => $request->name_output_dest,
                    'platform_dest' => $request->platform_output_dest,
                    'url_stream_dest' => $request->rtmp_output_server,
                    'key_stream_dest' => $request->rtmp_stream_key,
                    'active_stream_dest' => $request->status_output_dest
                ]);
            }
        } else {
            $responses = [
                'csrftoken' => csrf_token(),
                'messages' => '<div class="alert alert-danger"><span class="material-icons me-1">block</span>Stop Stream First Before Add Output Destination</div>',
                'success' => FALSE,
                'is_form' => FALSE
            ];
        }


        return response()->json($responses);
    }

    public function view_output_dest(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_stream = IngestStreamDestination::with('input_stream_data')->where('id', $request->id_output_dest)->first();
            if ($check_stream->user_id == Auth::user()->id) {
                $data['output_dest_data'] = $check_stream;

                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'html' => view('layouts.modal_layouts.view_output_dest', $data)->render()
                ];
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="material-icons me-1">cancel</span>You are not have access to view this output destination</div>'
                ];
            }
        }
        return response()->json($responses);
    }

    public function delete_output_dest(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_stream = IngestStreamDestination::with('input_stream_data')->where('id', $request->id_output_dest)->first();

            if ($check_stream->user_id == Auth::user()->id) {
                if ($check_stream->input_stream_data->is_live != TRUE) {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => TRUE,
                        'alert' => [
                            'icon' => 'success',
                            'title' => 'Delete Output Destination Success'
                        ]
                    ];

                    IngestStreamDestination::where('id', $check_stream['id'])
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
                        'title' => 'You are not have access to delete this output destination'
                    ]
                ];
            }
        }
        return response()->json($responses);
    }

    public function edit_output_dest(Request $request)
    {
        $method = $request->query('fetch');
        $check_stream = IngestStreamDestination::with('input_stream_data')->where('id', $request->id_output_dest)->first();
        if ($method == "show") {
            if ($check_stream->user_id == Auth::user()->id) {
                $data['output_dest_data'] = $check_stream;

                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'html' => view('layouts.modal_layouts.edit_output_dest', $data)->render()
                ];
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="material-icons me-1">cancel</span>You are not have access to view this output destination</div>'
                ];
            }
        } elseif ($method == "edit") {
            if ($check_stream->user_id == Auth::user()->id) {
                if ($check_stream->input_stream_data->is_live != TRUE) {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => [],
                        'success' => FALSE,
                        'is_form' => TRUE
                    ];
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'name_output_dest' => 'required|string|max:100',
                            'platform_output_dest' => 'required|in:youtube,twitch,custom',
                            'rtmp_output_server' => 'required|url|string|max:255|starts_with:rtmp://',
                            'rtmp_stream_key' => $check_stream->key_stream_dest == $request->rtmp_stream_key ? 'required|string|max:255' : 'required|unique:stream_ingest_dest,key_stream_dest|string|max:255',
                            'status_output_dest' => 'required|boolean'
                        ]
                    );

                    if ($validator->fails()) {
                        $responses['messages'] = $validator->errors()->all();
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'messages' => '<div class="alert alert-success">Edit output destination successfully</div>',
                            'is_form' => FALSE,
                            'success' => TRUE
                        ];

                        $check_dest = IngestStreamDestination::where('id', $request->id_output_dest)->first();
                        $check_dest->update([
                            'name_stream_dest' => $request->name_output_dest,
                            'platform_dest' => $request->platform_output_dest,
                            'url_stream_dest' => $request->rtmp_output_server,
                            'key_stream_dest' => $request->rtmp_stream_key,
                            'active_stream_dest' => $request->status_output_dest
                        ]);
                    }
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-danger"><span class="material-icons me-1">block</span>Stop Stream First Before Edit Output Destination</div>',
                        'success' => FALSE,
                        'is_form' => FALSE
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'is_form' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="material-icons me-1">cancel</span>You are not have access to edit this output destination</div>'
                ];
            }
        }

        return response()->json($responses);
    }
}
