<?php

namespace Different\Dwfw\app\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Cache;
use Different\Dwfw\app\Http\Requests\RoleStoreCrudRequest as StoreRequest;
use Different\Dwfw\app\Http\Requests\RoleUpdateCrudRequest as UpdateRequest;
use Different\Dwfw\app\Models\Permission;
use Different\Dwfw\app\Models\Role;

class RolesCrudController extends CrudController
{

    use CreateOperation;
    use ListOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        $this->role_model = $role_model = Role::class;
        $this->permission_model = $permission_model = Permission::class;

        $this->crud->setModel($role_model);
        $this->crud->setEntityNameStrings(trans('backpack::permissionmanager.role'), trans('backpack::permissionmanager.roles'));
        $this->crud->setRoute(backpack_url('role'));

        // deny access according to configuration file
        if (!config('dwfw.custom_role_handling')) {
            $this->crud->denyAccess('create');
            $this->crud->denyAccess('delete');
        }

        $this->crud->query->where('name', '!=', 'super admin');
    }

    public function setupListOperation()
    {
        /**
         * Show a column for the name of the role.
         */
        $this->crud->addColumn([
            'name' => 'display_name',
            'label' => trans('backpack::permissionmanager.name'),
            'type' => 'text',
        ]);

        /**
         * Show a column with the number of users that have that particular role.
         *
         * Note: To account for the fact that there can be thousands or millions
         * of users for a role, we did not use the `relationship_count` column,
         * but instead opted to append a fake `user_count` column to
         * the result, using Laravel's `withCount()` method.
         * That way, no users are loaded.
         */
        $this->crud->query->withCount('users');
        $this->crud->addColumn([
            'label' => trans('backpack::permissionmanager.users'),
            'type' => 'text',
            'name' => 'users_count',
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('users?role=["' . $entry->getKey() . '"]');
                },
            ],
            'suffix' => ' users',
        ]);

        /**
         * In case multiple guards are used, show a column for the guard.
         */
        if (config('backpack.permissionmanager.multiple_guards')) {
            $this->crud->addColumn([
                'name' => 'guard_name',
                'label' => trans('backpack::permissionmanager.guard_type'),
                'type' => 'text',
            ]);
        }

        /**
         * Show the exact permissions that role has.
         */
        $this->crud->addColumn([
            // n-n relationship (with pivot table)
            'label' => ucfirst(trans('backpack::permissionmanager.permission_plural')),
            'type' => 'select_multiple',
            'name' => 'permissions', // the method that defines the relationship in your Model
            'entity' => 'permissions', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => $this->permission_model, // foreign key model
            'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
        ]);
    }

    public function setupCreateOperation()
    {
        $this->addFields();
        $this->crud->setValidation(StoreRequest::class);

        //otherwise, changes won't have effect
        Cache::forget('spatie.permission.cache');
    }

    public function setupUpdateOperation()
    {
        $this->addFields();
        $this->crud->setValidation(UpdateRequest::class);

        //otherwise, changes won't have effect
        Cache::forget('spatie.permission.cache');
    }

    private function addFields()
    {
        $this->crud->addField([
            'name' => !config('dwfw.custom_role_handling') ? 'display_name' : 'name',
            'label' => trans('backpack::permissionmanager.name'),
            'type' => 'text',
            'attributes' => !config('dwfw.custom_role_handling') ? [
                'readonly' => 'readonly',
            ] : [],
        ]);

        if (config('backpack.permissionmanager.multiple_guards')) {
            $this->crud->addField([
                'name' => 'guard_name',
                'label' => trans('backpack::permissionmanager.guard_type'),
                'type' => 'select_from_array',
                'options' => $this->getGuardTypes(),
            ]);
        }
        /*$this->crud->addField([
            'name' => 'separator',
            'type' => 'custom_html',
            'value' => '<a target="blank" href="' . backpack_url('permission') . '"><i class="fas fa-info-circle"></i> ' . __('backpack::permissionmanager.permission_descriptions') . '</a>',
        ]);*/
        $this->crud->addField([
            'name' => 'permissions',
            'label' => ucfirst(trans('backpack::permissionmanager.permission_singular')),
            'type' => 'selectAll',            
            'attribute' => 'display_name',
            'entity' => 'permissions',   
            'pivot' => true,       
        ]);
        /*$this->crud->addField([
            'label' => ucfirst(trans('backpack::permissionmanager.permission_singular')),
            'type' => 'checklist',
            'name' => 'permissions',
            'entity' => 'permissions',
            'attribute' => 'display_name', // foreign key attribute that is shown to user
            'model' => $this->permission_model,
            'pivot' => true,
        ]);*/
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
