<?php

namespace Different\Dwfw\app\Traits;

use Different\Dwfw\app\Models\Log;
use Illuminate\Support\Facades\Auth;
use Request;

trait Loggable
{

    /**
     * @param  string  $route
     * @param  string  $event
     * @param  int|null  $entity_id
     * @param  null  $data
     * @param  string|null  $entity_type
     * @param  int|null  $user_id
     * @return Log|null
     */
    protected function baseLog(string $route, string $event, ?int $entity_id = null, $data = null, ?string $entity_type = null, ?int $user_id = null, string $status = 'OK'): ?Log
    {
        return Log::create([
            'user_id' => $user_id ? $user_id : (Auth::user() ? Auth::user()->id : null),
            'route' => $route,
            'entity_type' => $entity_type ?? $this->ENTITY_TYPE ?? Log::ET_SYSTEM,
            'entity_id' => $entity_id,
            'event' => $event,
            'data' => is_array($data) || is_object($data) ? json_encode($data) : $data,
            'ip_address' => Request::ip(),
            'status' => $status,
        ]);
    }
}
