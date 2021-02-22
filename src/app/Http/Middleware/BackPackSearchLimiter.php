<?php

namespace Different\Dwfw\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BackPackSearchLimiter
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
        if (isset($request->draw) && isset($request->length) && -1 == $request->length) {
            return response(null, 500);
        }

        return $next($request);
    }
}
