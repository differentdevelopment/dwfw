<?php

namespace Different\Dwfw\app\Traits;

use Different\Dwfw\app\Models\Log;
use Illuminate\Database\QueryException;
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
        $attributes = [
            'user_id' => $user_id ? $user_id : (Auth::user() ? Auth::user()->id : null),
            'route' => $route,
            'entity_type' => $entity_type ?? $this->ENTITY_TYPE ?? Log::ET_SYSTEM,
            'entity_id' => $entity_id,
            'event' => $event,
            'data' => is_array($data) || is_object($data) ? json_encode($data) : $data,
            'ip_address' => Request::ip(),
            'status' => $status,
        ];

        try {
            return Log::create($attributes);
        } catch (QueryException $e) {
            // The actor may have been removed within the same request (e.g. a user
            // deleting their own account): the "deleted" model event fires after the
            // row is gone, so logging Auth::id() would violate the logs.user_id
            // foreign key. Retry with a null actor, matching the FK's ON DELETE SET
            // NULL semantics, instead of failing the whole request.
            if ($attributes['user_id'] !== null && $this->isForeignKeyViolation($e)) {
                $attributes['user_id'] = null;

                return Log::create($attributes);
            }

            throw $e;
        }
    }

    /**
     * Integrity constraint violation (SQLSTATE 23000) with MySQL error 1452
     * (cannot add or update a child row: a foreign key constraint fails).
     */
    private function isForeignKeyViolation(QueryException $e): bool
    {
        return $e->getCode() === '23000' && (int) ($e->errorInfo[1] ?? 0) === 1452;
    }
}
