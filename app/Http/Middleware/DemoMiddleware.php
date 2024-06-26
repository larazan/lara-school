<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class DemoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $exclude_uri = array(
            '/login',
            '/api/student/login',
            '/api/parent/login',
            '/api/teacher/login',
            '/contact',
            '/api/student/submit-online-exam-answers'
        );
        $excludeEmails = [
            "info@crestwoodacademy.com",
            "alex.johnson@elementary.org"
        ];
        if (env('DEMO_MODE') && !$request->isMethod('get') && Auth::user() && !in_array(Auth::user()->email, $excludeEmails) && !in_array($request->getRequestUri(), $exclude_uri)) {
            // $excluded_ips = ['103.30.227.53','103.30.227.54']; // replace with the IPs you want to exclude
            // if (!in_array($request->ip(), $excluded_ips)) {
            return response()->json(array(
                'error'   => true,
                'message' => "This is not allowed in the Demo Version.",
                'code'    => 112
            ));
            // }
        }

        return $next($request);
    }
}
