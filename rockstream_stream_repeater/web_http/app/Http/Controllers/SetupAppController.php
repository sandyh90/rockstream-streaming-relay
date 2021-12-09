<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

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
                'is_active' => TRUE
            ]);
            return redirect()->route('login')->with('auth_msg', '<div class="alert alert-success text-center alert-dismissible fade show" role="alert"><span class="material-icons me-1">check_circle</span>Congratulations, Your setup account successfuly, Please login.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        }
    }
}
