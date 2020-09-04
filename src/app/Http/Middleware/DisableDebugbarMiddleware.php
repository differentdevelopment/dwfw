<?php


namespace Different\Dwfw\app\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class DisableDebugbarMiddleware
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
        if(class_exists(\Debugbar::class)) {
            \Debugbar::disable();
        }
        return $next($request);
    }
}
