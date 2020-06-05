<?php

namespace Different\Dwfw\app\Traits;

use Different\Dwfw\app\Models\Log;
use Illuminate\Support\Facades\Auth;
use Request;

trait LoggableAdmin
{
    protected $ENTITY_TYPE = Log::ET_SYSTEM;
    protected $ROUTE = 'admin';

    protected function log(string $event, ?int $entity_id = null, ?string $data = null, ?string $entity_type = null, ?int $user_id = null): void
    {
        if (!in_array($entity_type, [Log::ET_AUTH]) && !Auth::user()) {
            return;
        }
        Log::create([
            'user_id' => $user_id ? $user_id : (Auth::user() ? Auth::user()->id : null),
            'route' => $this->ROUTE,
            'entity_type' => $entity_type ?? $this->ENTITY_TYPE,
            'entity_id' => $entity_id,
            'event' => $event,
            'data' => $data,
            'ip_address' => Request::ip(),
        ]);
    }
}
