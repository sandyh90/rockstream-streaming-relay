<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;

class DiagnosticController extends Controller
{
    public function index()
    {
        return view('diagnostic');
    }

    public function get_failed_queue_data()
    {
        return DataTables::of(DB::table('failed_jobs')->orderBy('failed_at', 'desc')->get())
            ->addIndexColumn()
            ->addColumn('jobs_name', function ($data) {
                return json_decode($data->payload)->displayName;
            })
            ->addColumn('queue_name', function ($data) {
                return $data->queue;
            })
            ->editColumn('failed_at', function ($data) {
                return $data->failed_at;
            })
            ->addColumn('actions', function ($data) {
                return '<div class="btn-group">
                    <div class="btn btn-primary view-failed-queue" data-failed-queue-id="' . $data->id . '" data-bs-toggle="tooltip" title="View Failed Jobs"><span
                        class="bi bi-file-earmark-text"></span></div>
                    <div class="btn btn-danger delete-failed-queue" data-failed-queue-id="' . $data->id . '" data-bs-toggle="tooltip" title="Delete Failed Jobs"><span
                        class="bi bi-trash"></span></div>
                </div>
            ';
            })
            ->rawColumns(['actions'])
            ->removeColumn(['uuid', 'exception', 'connection', 'payload'])
            ->make(true);
    }

    public function view_failed_queue(Request $request)
    {
        if ($request->isMethod('post')) {
            $checkFailedQueue = DB::table('failed_jobs')->where('id', $request->id_failed_queue)->first();
            $data['failed_queue_data'] = $checkFailedQueue;

            $responses = [
                'csrftoken' => csrf_token(),
                'success' => TRUE,
                'html' => view('layouts.modal_layouts.view_failed_queue', $data)->render()
            ];
        }
        return response()->json($responses);
    }

    public function delete_failed_queue(Request $request)
    {
        if ($request->isMethod('post')) {
            $checkFailedQueue = DB::table('failed_jobs')->where('id', $request->id_failed_queue)->first();
            if ($checkFailedQueue) {
                DB::table('failed_jobs')->where(['id' => $checkFailedQueue->id])->delete();
                return $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'alert' => [
                        'icon' => 'success',
                        'title' => 'Delete Failed Job Success',
                    ]
                ];
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'warning',
                        'title' => 'Delete Failed Job Failed',
                    ]
                ];
            }
        }
        return response()->json($responses);
    }

    public function clear_failed_queue(Request $request)
    {
        if ($request->isMethod('post')) {
            $clearFailedQueue = DB::table('failed_jobs');
            if ($clearFailedQueue->exists()) {
                $clearFailedQueue->truncate();
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'alert' => [
                        'icon' => 'success',
                        'title' => 'Clear Failed Job Success',
                    ]
                ];
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'warning',
                        'title' => 'Clear Failed Job Failed, No Data Found',
                    ]
                ];
            }
        }
        return response()->json($responses);
    }
}
