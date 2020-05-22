<?php

namespace Different\Dwfw\app\Models\Traits;

use App\Models\User;

trait DwfwPartner
{
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getNameContactNameAttribute($value, string $separator = ' - '): string
    {
        return $this->name . $separator . $this->contact_name;
    }

}
