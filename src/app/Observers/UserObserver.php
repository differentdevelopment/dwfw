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
        return $user->load('roles')->load('partner');
    }
}
