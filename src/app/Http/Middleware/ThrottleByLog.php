<?php

namespace Different\Dwfw\app\Http\Middleware;

use Closure;
use Different\Dwfw\app\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ThrottleByLog
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param int $max_count
     * @param int $treshold
     * @return mixed
     */
    public function handle(Request $request, Closure $next, int $max_count, int $treshold)
    {
        if ($max_count <= Log::getCountForEventsByTreshold(array_slice(func_get_args(), 4), $treshold))
            return response(null, 429);
        return $next($request);
    }

}
