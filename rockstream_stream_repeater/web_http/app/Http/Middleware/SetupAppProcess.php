<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\User;

class SetupAppProcess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the app is already setup or not and redirect to setup page if not setup yet
        if (!User::exists()) {
            return redirect()->route('setup');
        } else {
            return $next($request);
        }
    }
}
