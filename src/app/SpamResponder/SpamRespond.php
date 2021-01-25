<?php


namespace App\SpamResponder;

use Closure;
use Illuminate\Http\Request;
use Spatie\Honeypot\SpamResponder\SpamResponder;

class SpamRespond implements SpamResponder
{
    public function respond(Request $request, Closure $next)
    {
        return redirect('https://www.youtube.com/watch/dQw4w9WgXcQ');
    }
}
