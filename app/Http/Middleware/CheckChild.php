<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckChild
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $children = $request->user()->guardianRelationChild()->where('id', $request->child_id)->first();
        if (empty($children)) {
            return response()->json(array(
                'error'   => true,
                'message' => "Invalid Child ID Passed.",
                'code'    => 105,
            ));
        }
        
        return $next($request);
    }
}
