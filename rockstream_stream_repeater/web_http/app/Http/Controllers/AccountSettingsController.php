<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Session_Manage;

class AccountSettingsController extends Controller
{
    public function settings()
    {
        $session = new Session_Manage;
        $data = [
            'session_list' => $session->get_session_data(Auth::id())
        ];
        return view('account_settings', $data);
    }

    public function update_profile(Request $request)
    {
        if ($request->isMethod('POST')) {
            $user_data = User::where('id', Auth::id())->first();

            $validator = Validator::make(
                $request->all(),
                [
                    'fullname' => 'required|string',
                    'username' => $user_data->username == $request->username ? 'required|string' : 'required|unique:users,username|string',
                ]
            );

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {
                User::where('id', $user_data['id'])->update(
                    [
                        'name' => $request->fullname,
                        'username' => $request->username
                    ]
                );
            }
            return redirect()->back()->with('setting_msg', '<div class="alert alert-success text-center alert-dismissible fade show" role="alert"><span class="bi bi-check-circle me-1"></span>Your profile successfully change.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        }
    }

    public function update_password(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'old_password' => 'required',
                    'new_password' => 'required|min:8|same:new_password_confirm',
                    'new_password_confirm' => 'required|min:8|same:new_password',
                ]
            );

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {
                $matchpass = [
                    'old' => $request->old_password,
                    'new' => $request->new_password
                ];
                if ($matchpass['old'] == $matchpass['new']) {
                    return redirect()->back()->with('setting_msg', '<div class="alert alert-danger text-center alert-dismissible fade show" role="alert"><span class="bi bi-x-circle me-1"></span>New password must be different from old password.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                } elseif (!Hash::check($matchpass['old'], Auth::user()->password)) {
                    return redirect()->back()->with('setting_msg', '<div class="alert alert-danger text-center alert-dismissible fade show" role="alert"><span class="bi bi-x-circle me-1"></span>Old password not match.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                } else {
                    User::where('id', Auth::id())->update(['password' => Hash::make($request->new_password)]);
                    Auth::logoutOtherDevices($request->old_password);
                    return redirect()->back()->with('setting_msg', '<div class="alert alert-success text-center alert-dismissible fade show" role="alert"><span class="bi bi-check-circle me-1"></span>Your password has been change.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            }
        }
    }

    public function logout_all_session(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'logout_password' => 'required'
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with('setting_msg', '<div class="alert alert-danger text-center alert-dismissible fade show" role="alert">' . $validator->errors()->first() . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        } else {
            if (Hash::check($request->logout_password, Auth::user()->password)) {
                $check_data = Session_Manage::where('user_id', Auth::id())->first();

                if ($check_data) {
                    Auth::logoutOtherDevices($request->logout_password);
                    Session_Manage::where('user_id', Auth::id())->delete();
                    return redirect()->route('login');
                }
            } else {
                return redirect()->back()->with('setting_msg', '<div class="alert alert-danger text-center alert-dismissible fade show" role="alert"><span class="bi bi-x-circle me-1"></span>Your input password wrong.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            }
        }
    }
}
