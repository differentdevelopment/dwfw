<?php

namespace Different\Dwfw\app\Http\Middleware;

use Different\Dwfw\app\Models\TimeZone;
use Closure;
use Exception;

class ConvertIdToTimeZone
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth()) {
            try {
                Auth()->user()->timezone = TimeZone::query()->find(Auth()->user()->timezone_id);
            } catch (Exception $e) {

            }
        }
        return $next($request);
    }
}
