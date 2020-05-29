<?php

namespace Different\Dwfw\app\Http\Controllers;

use Alert;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\PermissionManager\app\Http\Controllers\UserCrudController;
use Carbon\Carbon;
use Different\Dwfw\app\Models\TimeZone;

class UsersCrudController extends UserCrudController
{
    use ShowOperation {
        show as traitShow;
    }

    public function setup()
    {
        parent::setup();
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
        ]);
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
        if (!$user->email_verified_at) {
            $user->update(['email_verified_at' => Carbon::now()]);
            Alert::success(__('dwfw::users.verified'))->flash();
        } else {
            Alert::error(__('dwfw::users.already_verified'))->flash();
        }
        return redirect(backpack_url('users'));
    }
}
