<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

use Yajra\DataTables\DataTables;

class ManageUsersController extends Controller
{
    public function index()
    {
        return view('manage_users');
    }

    public function get_manage_users(Request $request)
    {
        return DataTables::of(User::get())
            ->addIndexColumn()
            ->editColumn('is_operator', function ($data) {
                if ($data->is_operator == TRUE) {
                    return '<div class="text-danger"><span class="bi bi-wrench-adjustable-circle me-1"></span>Operator</div>';
                } else {
                    return '<div class="text-success"><span class="bi bi-person-circle me-1"></span>Non Operator</div>';
                }
            })
            ->editColumn('is_active', function ($data) {
                if ($data->is_active == TRUE) {
                    return '<div class="text-success"><span class="bi bi-check-circle me-1"></span>Active</div>';
                } else {
                    return '<div class="text-danger"><span class="bi bi-x-circle me-1"></span>Not Active</div>';
                }
            })
            ->addColumn('actions', function ($data) {
                return '<div class="btn-group">
                <div class="btn btn-primary view-users-data" data-users-id="' . $data->id . '" data-bs-toggle="tooltip" title="View User Data"><span
                    class="bi bi-file-earmark-text"></span></div>
                <div class="btn btn-success edit-users-data" data-users-id="' . $data->id . '" data-bs-toggle="tooltip" title="Edit User Data"><span
                    class="bi bi-pencil-square"></span></div>
                <div class="btn btn-danger delete-users-data" data-users-id="' . $data->id . '" data-bs-toggle="tooltip" title="Delete User Data"><span
                    class="bi bi-trash"></span></div>
            </div>
        ';
            })
            ->rawColumns(['actions', 'is_active', 'is_operator'])
            ->make(true);
    }

    public function add_users_data(Request $request)
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
                'name_user' => 'required|string|max:255',
                'username_user' => 'required|string|unique:users,username|max:50',
                'password_user' => 'required|string|min:8',
                'operator_user' => 'required|boolean',
                'status_user' => 'required|boolean'
            ]
        );

        if ($validator->fails()) {
            $responses['messages'] = $validator->errors()->all();
        } else {
            $responses = [
                'csrftoken' => csrf_token(),
                'messages' => '<div class="alert alert-success">Add user successfully</div>',
                'success' => TRUE,
                'isForm' => FALSE
            ];

            User::create([
                'name' => $request->name_user,
                'username' => $request->username_user,
                'password' => Hash::make($request->password_user),
                'is_operator' => $request->operator_user,
                'is_active' => $request->status_user,
            ]);
        }

        return response()->json($responses);
    }


    public function view_users_data(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_user = User::where('id', $request->id_users);
            if ($check_user->exists()) {
                $data['users_data'] = $check_user->first();

                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'html' => view('layouts.modal_layouts.view_users_data', $data)->render()
                ];
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>This user data not available</div>'
                ];
            }
        }
        return response()->json($responses);
    }

    public function delete_users_data(Request $request)
    {
        if ($request->isMethod('post')) {
            $check_user = User::where('id', $request->id_users);

            if ($check_user->exists()) {
                $get_data = $check_user->first();
                User::where('id', $get_data['id'])
                    ->delete();

                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'alert' => [
                        'icon' => 'success',
                        'title' => 'Delete User Success'
                    ]
                ];
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'warning',
                        'title' => 'Delete User Failed, User Not Found'
                    ]
                ];
            }
        }
        return response()->json($responses);
    }

    public function edit_users_data(Request $request)
    {
        $method = $request->query('fetch');
        $check_user = User::where('id', $request->id_users);
        if ($method == "show") {
            if ($check_user->exists()) {
                $data['users_data'] = $check_user->first();

                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'html' => view('layouts.modal_layouts.edit_users_data', $data)->render()
                ];
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>This user data not available</div>'
                ];
            }
        } elseif ($method == "edit") {
            $responses = [
                'csrftoken' => csrf_token(),
                'messages' => [],
                'success' => FALSE,
                'isForm' => TRUE
            ];
            if ($check_user->exists()) {
                $data_check = $check_user->first();
                $validator = Validator::make(
                    $request->all(),
                    [
                        'name_user' => 'required|string|max:255',
                        'username_user' => $data_check->username == $request->username_user ? 'required|string|max:50' : 'required|string|unique:users,username|max:50',
                        'password_user' => $request->filled('password_user') ? 'required|string|min:8' : 'nullable',
                        'operator_user' => 'required|boolean',
                        'status_user' => 'required|boolean'
                    ]
                );

                if ($validator->fails()) {
                    $responses['messages'] = $validator->errors()->all();
                } else {
                    if ($data_check->id == Auth::id() && $request->operator_user != $data_check->is_operator) {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>You can not change operator.</div>',
                            'success' => FALSE,
                            'isForm' => FALSE
                        ];
                    } else {
                        $responses = [
                            'csrftoken' => csrf_token(),
                            'messages' => '<div class="alert alert-success">Edit user successfully</div>',
                            'isForm' => FALSE,
                            'success' => TRUE
                        ];

                        $updateUser = User::where('id', $request->id_users);
                        if ($request->filled('password_user')) {
                            $updateUser->update(['password' => Hash::make($request->password_user)]);
                        }

                        // Prevent now user to change the status of the admin
                        if ($data_check->id != Auth::id()) {
                            $updateUser->update(['is_operator' => $request->operator_user]);
                        }

                        $updateUser->update([
                            'name' => $request->name_user,
                            'username' => $data_check->username == $request->username_user ? $data_check->username : $request->username_user,
                            'is_active' => $request->status_user,
                        ]);
                    }
                }
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'messages' => '<div class="alert alert-danger"><span class="bi bi-x-circle me-1"></span>This user data not available</div>',
                    'isForm' => FALSE,
                    'success' => FALSE
                ];
            }
        }

        return response()->json($responses);
    }
}
