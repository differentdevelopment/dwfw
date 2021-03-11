<?php

namespace Different\Dwfw\app\Http\Controllers\Traits;

use Different\Dwfw\app\Models\Permission;
use Illuminate\Support\Str;

trait CheckCrudPermissions
{
//    public function checkCrudPermissions()
//    {
//        abort_if('\App\Models\Entity' == $this->crud->model, 500, 'checkCrudPermissions() should be after setting crud model!');
//
//        $backpack_user = backpack_user();
//        $entity_name = Str::plural(Str::lower(class_basename($this->crud->model::class)));
//        foreach (Permission::query()->where('name', 'like', $entity_name . '.%')->get() as $permission) {
//            if (!$backpack_user->can($permission->name)) $this->crud->denyAccess(Str::replaceFirst($entity_name . '.', '', $permission->name));
//        }
//    }

    public function checkCrudPermissionsByPermission($permission)
    {
        abort_if('\App\Models\Entity' == $this->crud->model, 500, 'checkCrudPermissions() should be after setting crud model!');

        $entity_name = Str::of(class_basename($this->crud->model::class))->snake()->lower()->plural();
        if (backpack_user()->cannot($entity_name . '.' . $permission)) {
            $this->crud->denyAccess(['list', 'create', 'update', 'delete', 'show']);
        }
    }

//    public function checkAccount($relation_name = 'accounts')
//    {
//        $this->crud->addClause('whereHas', $relation_name, function ($query) {
//            if (session('account_id') == -1) {
//                $query->whereIn('accounts.id', session('account_ids'));
//            } else {
//                $query->where('accounts.id', session('account_id'));
//            }
//        });
//    }
}
