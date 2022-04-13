<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

use App\Models\User;

class SetupAppController extends Controller
{
    public function index()
    {
        if (Auth::check() || User::exists()) {
            return redirect()->route('home');
        }
        return view('setup.appsetup');
    }

    public function setup_app(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'fullname' => 'required|string|max:255',
                'username' => 'required|unique:users,username|string|max:50',
                'password' => 'required|min:8|same:password_confirm',
                'password_confirm' => 'required|min:8|same:password',
            ]
        );

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        } else {
            User::create([
                'name' => $request->fullname,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'is_operator' => (User::count() <= 1) && !User::exists() ? TRUE : FALSE,
                'is_active' => TRUE
            ]);

            //Generate key for app and save it to .env file

            return redirect()->route('login')->with('auth_msg', '<div class="alert alert-success text-center alert-dismissible fade show" role="alert"><span class="bi bi-check-circle me-1"></span>Congratulations, Your setup account successfuly, Please login.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        }
    }

    public function generate_appkey()
    {
        if (Auth::check() || User::exists()) {
            $responses = [
                'csrftoken' => csrf_token(),
                'success' => FALSE,
                'alert' => [
                    'icon' => 'warning',
                    'title' => 'Access Denied',
                    'text' => 'You dont\'t have access to run this action.'
                ]
            ];
        } else {
            Artisan::call('key:generate');
            $responses = [
                'csrftoken' => csrf_token(),
                'success' => FALSE,
                'alert' => [
                    'icon' => 'success',
                    'title' => 'Generated App Key Successfully',
                    'text' => 'APP_KEY successfully generated.'
                ]
            ];
        }
        return response()->json($responses);
    }
}
