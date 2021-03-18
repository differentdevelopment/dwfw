<?php

namespace Different\Dwfw\app\Models\Traits;

use Different\Dwfw\app\Scopes\AccountScope;

trait DwfwAccounts
{
    protected static function booted()
    {
        static::addGlobalScope(new AccountScope());
    }
}
