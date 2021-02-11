<?php

namespace Different\Dwfw\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Spatie\Permission\Models\Permission as OriginalPermission;

/**
 * Class Permission
 * @package Different\Dwfw\app\Models
 * @property string $name
 * @property string $guard_name
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */
class Permission extends OriginalPermission
{
    use CrudTrait;

    protected $fillable = ['name', 'guard_name', 'updated_at', 'created_at'];

    public function getDisplayNameAttribute()
    {
        return __('backpack::permissionmanager.' . $this->name);
    }

    public function getDescriptionAttribute()
    {
        return __('backpack::permissionmanager.' . $this->name . ' description');
    }
}
