<?php

namespace Different\Dwfw\app\Http\Controllers;

use Alert;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\PermissionManager\app\Http\Controllers\UserCrudController;
use Different\Dwfw\app\Http\Controllers\Traits\ColumnFaker;
use Different\Dwfw\app\Http\Controllers\Traits\FileUpload;
use Different\Dwfw\app\Models\TimeZone;
use Different\Dwfw\app\Traits\LoggableAdmin;

class UsersCrudController extends UserCrudController
{
    use FileUpload;
    use ColumnFaker;
    use ShowOperation {
        show as traitShow;
    }
    use CreateOperation {
        store as traitStore;
    }
    use UpdateOperation {
        update as traitUpdate;
    }
    use LoggableAdmin;

    public function setup()
    {
        parent::setup();
        $this->crud->setModel(User::class);
        $this->crud->setRoute(backpack_url('users'));
        $this->crud->addButton('line', 'verify', 'view', 'dwfw::crud.buttons.users.verify', 'beginning');
    }

    public function show($id)
    {
        $this->crud->hasAccessOrFail('list');
        $user = User::find($id);
        $this->data['crud'] = $this->crud;
        $this->data['user'] = $user;
        return view('admin.users.show', $this->data);
    }

    public function setupListOperation()
    {
        parent::setupListOperation();
        $this->crud->addColumn([
            'name' => 'partner',
            'label' => __('dwfw::partners.partner'),
            'type' => 'select',
            'entity' => 'partner',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('partner', function ($q) use ($column, $searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('contact_name', 'like', '%' . $searchTerm . '%');
                });
            }
        ])->afterColumn('name');
        $this->crud->addColumn([
            'name' => 'email_verified_at',
            'label' => __('dwfw::users.verified_at'),
            'type' => 'date',
        ])->afterColumn('partner');
    }

    protected function addUserFields()
    {
        parent::addUserFields();
        $this->crud->addField([
            'name' => 'partner_id',
            'label' => __('dwfw::partners.partner'),
            'type' => 'select',
            'entity' => 'partner',
            'attribute' => 'name_contact_name',
            'options' => (function ($query) {
                return $query->orderBy('name', 'ASC')->orderBy('contact_name', 'ASC')->get();
            }),
            'wrapper' => [
                'class' => 'form-group col-12 col-sm-12',
            ],
        ])->beforeField('name');
        $this->crud->addFields([
            [
                'name' => 'email_verified_at',
                'label' => __('dwfw::users.verified_at'),
                'type' => 'date',
                'wrapper' => [
                    'class' => 'form-group col-12 col-sm-6',
                ],
            ],
            [
                'name' => 'timezone_id',
                'label' => __('dwfw::timezones.timezone'),
                'type' => 'select',
                'entity' => 'timezone',
                'attribute' => 'name_with_diff',
                'model' => 'Different\Dwfw\app\Models\TimeZone',
                'options' => (function ($query) {
                    return $query->orderBy('name', 'ASC')->get();
                }),
                'default' => TimeZone::DEFAULT_TIMEZONE_CODE,
                'wrapper' => [
                    'class' => 'form-group col-12 col-sm-6',
                ],
            ],
            [
                'name' => 'last_device',
                'label' => __('dwfw::users.last_device'),
                'type' => 'text',
                'wrapper' => [
                    'class' => 'form-group col-12 col-sm-6',
                ],
            ],
            [
                'name' => 'profile_image',
                'label' => __('dwfw::users.profile_image'),
                'type' => 'upload',
                'attribute' => 'original_name',
                'upload' => true,
                'wrapper' => [
                    'class' => 'form-group col-12 col-sm-6',
                ],
            ],

        ]);
    }

    public function store()
    {
        $this->handleFileUpload('profile_image', null, 'users');
        return parent::store();
    }

    public function update()
    {
        $this->handleFileUpload('profile_image', null, 'users');
        return parent::update();
    }

    protected function getFilters()
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOM NON-BACKPACK METHODS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | VERIFY USER
    |--------------------------------------------------------------------------
    */

    public function verifyUser(User $user)
    {
        $user->verify();
        Alert::success(__('dwfw::users.verified'))->flash();
        return redirect(backpack_url('users'));
    }

    public function abortUserGrid()
    {
        return abort(404);
    }
}
