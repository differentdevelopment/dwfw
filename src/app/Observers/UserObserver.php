<?php

namespace Different\Dwfw\app\Observers;

use App\Models\User;
use Different\Dwfw\app\Models\Log;

class UserObserver extends Observer
{
    protected string $ENTITY_TYPE = Log::ET_USER;

    /**
     * @param User $user
     * @return string
     */
    public function getData($user)
    {
        return $this->implodeData([
            $user->name,
            $user->email,
            $user->partner ? $user->partner->name_contact_name : '',
            implode(', ', $user->roles->pluck('name')->toArray()),
        ]);
    }
}
