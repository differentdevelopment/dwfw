<?php

namespace Different\Dwfw\app\Http\Middleware;

use Closure;

class CheckIpMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(in_array($request->ip(), config('checkIp.block_list'))) {
            return response()->json(['your ip address is not valid.'], 403);
        } elseif (!in_array($request->ip(), config('checkIp.allow_list')) && !in_array('*', config('checkIp.allow_list'))) {
            return response()->json(['your ip address is not valid.'], 403);
        }
        return $next($request);
    }
}
