<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

use Carbon\Carbon;

class AuthController extends Controller
{
    public function showlogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|string',
                'password' => 'required|string'
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        } else {

            $data = [
                'username'  => $request->input('username'),
                'password'  => $request->input('password'),
                'is_active' => TRUE
            ];

            if (Auth::attempt($data, $request->input('rememberme'))) {

                $request->session()->regenerate();

                User::where('id', Auth::user()->id)->update(['last_login' => Carbon::now()]);
                return redirect()->to('/');
            } else {
                return redirect()->route('login')->with('auth_msg', '<div class="alert alert-danger text-center alert-dismissible fade show" role="alert"><span class="material-icons me-1">warning</span>Username or password wrong.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            }
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
