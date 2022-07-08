<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Component\ServicesCore;

use App\Models\IngestStreamDestination;
use App\Models\StreamInput;

use Yajra\DataTables\DataTables;

class IngestDestinationController extends Controller
{
    public function get_output_dest(Request $request)
    {
        return DataTables::of(IngestStreamDestination::with('input_stream_data')->whereHas('input_stream_data', function ($query) use ($request) {
            return $query->where('identifier_stream', $request->id_input_stream);
        })->where('user_id', Auth::id())->get())
            ->addIndexColumn()
            ->editColumn('platform_dest', function ($data) {
                return ServicesCore::getServiceIcons($data->platform_dest) . (Str::contains($data->url_stream_dest, 'rtmps://') ? '<span class="bi bi-exclamation-circle text-danger ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="RTMPS protocol for now is not supported yet, you may suffer some errors if use it"></span>' : '<span class="bi bi-check-circle text-success ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Everything is okay"></span>');
            })
            ->addColumn('is_active', function ($data) {
                if ($data->active_stream_dest == TRUE) {
                    return '<div class="text-success"><span class="bi bi-check-circle me-1"></span>Active</div>';
                } else {
                    return '<div class="text-danger"><span class="bi bi-x-circle me-1"></span>Not Active</div>';
                }
            })
            ->addColumn('actions', function ($data) {
                if ($data->input_stream_data->is_live != TRUE) {
                    return '<div class="btn-group">
                <div class="btn btn-primary view-output-dest" data-dest-output-id="' . $data->id . '" data-toggle="tooltip" title="View Output Destination"><span
                    class="bi bi-file-earmark-text"></span></div>
                <div class="btn btn-success edit-output-dest" data-dest-output-id="' . $data->id . '" data-toggle="tooltip" title="Edit Output Destination"><span
                    class="bi bi-pencil-square"></span></div>
                <div class="btn btn-danger delete-output-dest" data-dest-output-id="' . $data->id . '" data-toggle="tooltip" title="Delete Output Destination"><span
                    class="bi bi-trash"></span></div>
                </div>
                ';
                } else {
                    return '<div class="text-danger"><span class="bi bi-x-circle me-1"></span>Stream Active</div>';
                };
            })
            ->rawColumns(['actions', 'is_active', 'platform_dest'])
            ->make(true);
    }

    public function add_output_dest(Request $request)
    {
        $check_stream = StreamInput::with('ingest_destination_data')->where('id', $request->input_stream_id)->first();
        $check_service = collect(ServicesCore::getServices())->where('id', $request->platform_output_dest)->first();
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
                    'name_output_dest' => 'required|string|max:255',
                    'platform_output_dest' => [
                        'required',
                        Rule::in(array_column(ServicesCore::getServices(), 'id'))
                    ],
                    'select_endpoint_server' => $request->filled('rtmp_output_server') ? 'prohibited|nullable|string|max:255|starts_with:rtmp://,rtmps://' : 'required|string|max:255|starts_with:rtmp://,rtmps://',
                    'rtmp_output_server' => $request->filled('select_endpoint_server') ? 'prohibited|nullable|string|max:255|starts_with:rtmp://,rtmps://' : 'required|string|max:255|starts_with:rtmp://,rtmps://',
                    'rtmp_stream_key' => empty($check_stream->ingest_destination_data()->first()->key_stream_dest) ? 'required|string|max:355' : ($check_stream->ingest_destination_data()->first()->key_stream_dest != $request->rtmp_stream_key ? 'required|string|max:355' : 'required|unique:stream_ingest_dest,key_stream_dest|string|max:355'),
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
                    'user_id' => (Auth::check() ? Auth::id() : NULL),
                    'name_stream_dest' => $request->name_output_dest,
                    'platform_dest' => $check_service['code_data'],
                    'url_stream_dest' => $check_service['is_manual_input'] == FALSE ? $request->select_endpoint_server : $request->rtmp_output_server,
                    'key_stream_dest' => $request->rtmp_stream_key,
                    'active_stream_dest' => $request->status_output_dest
                ]);

                Artisan::call('nginxrtmp:regenconfig');
            }
        } else {
            $responses = [
                'csrftoken' => csrf_token(),
                'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>Stop Stream First Before Add Output Destination</div>',
                'success' => FALSE,
                'isForm' => FALSE
            ];
        }


        return response()->json($responses);
    }

    public function view_output_dest(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_stream = IngestStreamDestination::with('input_stream_data')->where('id', $request->id_output_dest)->first();
            if ($check_stream) {
                if ($check_stream->user_id == Auth::id()) {
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
                        'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You are not have access to view this output destination</div>'
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>Output destination not found</div>'
                ];
            }
        }
        return response()->json($responses);
    }

    public function delete_output_dest(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_stream = IngestStreamDestination::with('input_stream_data')->where('id', $request->id_output_dest)->first();

            if ($check_stream) {
                if ($check_stream->user_id == Auth::id()) {
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
                            'title' => 'You are not have access to delete this output destination'
                        ]
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'warning',
                        'title' => 'Output destination not found'
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
        $check_service = collect(ServicesCore::getServices())->where('id', $request->platform_output_dest)->first();
        if ($method == "show") {
            if ($check_stream) {
                if ($check_stream->user_id == Auth::id()) {
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
                        'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You are not have access to view this output destination</div>'
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>Output destination not found</div>'
                ];
            }
        } elseif ($method == "edit") {
            if ($check_stream) {
                if ($check_stream->user_id == Auth::id()) {
                    if ($check_stream->input_stream_data->is_live != TRUE) {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'messages' => [],
                            'success' => FALSE,
                            'isForm' => TRUE
                        ];
                        $validator = Validator::make(
                            $request->all(),
                            [
                                'name_output_dest' => 'required|string|max:255',
                                'platform_output_dest' => [
                                    'required',
                                    Rule::in(array_column(ServicesCore::getServices(), 'id'))
                                ],
                                'select_endpoint_server' => $request->filled('rtmp_output_server') ? 'prohibited|nullable|string|max:255|starts_with:rtmp://,rtmps://' : 'required|string|max:255|starts_with:rtmp://,rtmps://',
                                'rtmp_output_server' => $request->filled('select_endpoint_server') ? 'prohibited|nullable|string|max:255|starts_with:rtmp://,rtmps://' : 'required|string|max:255|starts_with:rtmp://,rtmps://',
                                'rtmp_stream_key' => $check_stream->key_stream_dest == $request->rtmp_stream_key ? 'required|string|max:355' : 'required|unique:stream_ingest_dest,key_stream_dest|string|max:355',
                                'status_output_dest' => 'required|boolean'
                            ]
                        );

                        if ($validator->fails()) {
                            $responses['messages'] = $validator->errors()->all();
                        } else {
                            $responses = [
                                'csrftoken' => csrf_token(),
                                'messages' => '<div class="alert alert-success">Edit output destination successfully</div>',
                                'isForm' => FALSE,
                                'success' => TRUE
                            ];

                            IngestStreamDestination::where('id', $request->id_output_dest)->update([
                                'name_stream_dest' => $request->name_output_dest,
                                'platform_dest' => $check_service['code_data'],
                                'url_stream_dest' => $check_service['is_manual_input'] == FALSE ? $request->select_endpoint_server : $request->rtmp_output_server,
                                'key_stream_dest' => $request->rtmp_stream_key,
                                'active_stream_dest' => $request->status_output_dest
                            ]);

                            if ((($check_service['is_manual_input'] == FALSE ? $request->select_endpoint_server : $request->rtmp_output_server) != $check_stream->url_stream_dest) || ($request->rtmp_stream_key != $check_stream->key_stream_dest) || ($request->status_output_dest != $check_stream->active_stream_dest)) {
                                Artisan::call('nginxrtmp:regenconfig');
                            }
                        }
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>Stop Stream First Before Edit Output Destination</div>',
                            'success' => FALSE,
                            'isForm' => FALSE
                        ];
                    }
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'success' => FALSE,
                        'isForm' => FALSE,
                        'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You are not have access to edit this output destination</div>'
                    ];
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'isForm' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>Output destination not found</div>'
                ];
            }
        }

        return response()->json($responses);
    }

    public function fetch_endpoint_output_dest(Request $request)
    {
        $getService = [];
        $fetch_stream = collect(ServicesCore::getServices())->where('id', $request->id_platform)->first();
        foreach ($fetch_stream['server_list'] as $key => $value) {
            $getService[] = ['rtmp_address' => $value, 'name_endpoint' => $key];
        }
        $responses = [
            'csrftoken' => csrf_token(),
            'manual_input' => $fetch_stream['is_manual_input'],
            'services' => $getService
        ];
        return response()->json($responses);
    }
}
