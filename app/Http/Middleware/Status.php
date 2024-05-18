<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Status
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->status != 1) {
            Auth::logout();
            $request->session()->flush();
            $request->session()->regenerate();
            return redirect()->route('login')->withErrors(trans('your_account_has_been_deactivated_please_contact_admin'));
        }
        
        return $next($request);
    }
}
