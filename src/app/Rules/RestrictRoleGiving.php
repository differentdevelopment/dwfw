<?php

namespace Different\Dwfw\app\Rules;

use Backpack\PermissionManager\app\Models\Role;
use Illuminate\Contracts\Validation\Rule;

class RestrictRoleGiving implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $super_admin_id = Role::query()->where('name', 'super admin')->first()->id;
        if(in_array((string) $super_admin_id, $value) && !auth()->user()->hasRole('super admin')){
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('dwfw::users.super_admin_role_error');
    }
}
