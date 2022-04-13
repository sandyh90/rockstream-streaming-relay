<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AjaxCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->ajax()) {
            return abort(403, 'Only ajax request is allowed.');
        }
        return $next($request);
    }
}
