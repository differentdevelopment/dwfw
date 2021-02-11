<?php

namespace Different\Dwfw\app\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\PermissionManager\app\Http\Requests\PermissionStoreCrudRequest as StoreRequest;
use Backpack\PermissionManager\app\Http\Requests\PermissionUpdateCrudRequest as UpdateRequest;
use Different\Dwfw\app\Models\Permission;

// VALIDATION

class PermissionCrudController extends CrudController
{
    use ListOperation;

    public function setup()
    {
        $this->role_model = $role_model = config('backpack.permissionmanager.models.role');
        $this->permission_model = $permission_model = config('backpack.permissionmanager.models.permission');

        $this->crud->setModel($permission_model);
        $this->crud->setEntityNameStrings(trans('backpack::permissionmanager.permission_singular'), trans('backpack::permissionmanager.permission_plural'));
        $this->crud->setRoute(backpack_url('permission'));

        $this->crud->addFilter([
            'name'  => 'name',
            'type'  => 'select2',
            'label' => trans('backpack::permissionmanager.name'),
        ], function () {
            $values = [];
            $permissions = Permission::query()->get();
            $permissions->each(function($permission) use (&$values){
                $values += [$permission->name => $permission->display_name];
            });
            return $values;
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'name', $value);
        });
    }

    public function setupListOperation()
    {
        $this->crud->addColumn([
            'name'  => 'display_name',
            'label' => trans('backpack::permissionmanager.name'),
            'type'  => 'text',
        ]);

        $this->crud->addColumn([
            'name'  => 'description',
            'label' => trans('backpack::permissionmanager.description'),
            'type'  => 'markdown',
        ]);

        if (config('backpack.permissionmanager.multiple_guards')) {
            $this->crud->addColumn([
                'name'  => 'guard_name',
                'label' => trans('backpack::permissionmanager.guard_type'),
                'type'  => 'text',
            ]);
        }
    }

    /*
     * Get an array list of all available guard types
     * that have been defined in app/config/auth.php
     *
     * @return array
     **/
    private function getGuardTypes()
    {
        $guards = config('auth.guards');

        $returnable = [];
        foreach ($guards as $key => $details) {
            $returnable[$key] = $key;
        }

        return $returnable;
    }
}
