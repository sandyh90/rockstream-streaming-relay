<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class ResetPasswordController extends Controller
{
    public function showresetpassword()
    {
        if (Auth::check()) {
            return route('home');
        }
        return view('auth.resetpassword');
    }

    public function resetpassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|string',
                'password' => 'required|string|min:8'
            ]
        );

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        } else {
            $user = User::where('username', $request->username);
            if ($user->exists()) {
                $user->update(['password' => Hash::make($request->password)]);
                return redirect()->route('login')->with('auth_msg', '<div class="alert alert-success text-center alert-dismissible fade show" role="alert"><span class="material-icons me-1">check_circle</span>Congratulations, Your reset password successfuly, Please login.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            } else {
                return back()->withInput()->with('auth_msg', '<div class="alert alert-danger text-center alert-dismissible fade show" role="alert"><span class="material-icons me-1">warning</span>This account are not found.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            }
        }
    }
}
