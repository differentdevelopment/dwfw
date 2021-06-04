<?php

namespace Different\Dwfw\app\Http\Controllers;

use Alert;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Different\Dwfw\app\Http\Controllers\Traits\CheckCrudPermissions;
use Different\Dwfw\app\Http\Controllers\Traits\ColumnFaker;
use Different\Dwfw\app\Http\Controllers\Traits\FileUpload;
use Different\Dwfw\app\Http\Requests\AccountChangeRequest;
use Different\Dwfw\app\Http\Requests\UserStoreRequest;
use Different\Dwfw\app\Http\Requests\UserUpdateRequest;
use Different\Dwfw\app\Models\Account;
use Different\Dwfw\app\Models\Role;
use Different\Dwfw\app\Models\TimeZone;
use Different\Dwfw\app\Scopes\AccountScope;
use Different\Dwfw\app\Traits\LoggableAdmin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Middlewares\PermissionMiddleware;

class UsersCrudController extends BaseCrudController
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
    use ListOperation;
    use CheckCrudPermissions;
    use FetchOperation;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(PermissionMiddleware::class . ':manage users');
    }

    public function setup()
    {
        $this->crud->setRoute(backpack_url('users'));
        $this->crud->setEntityNameStrings(trans('backpack::permissionmanager.user'), trans('backpack::permissionmanager.users'));
        $this->crud->setModel(User::class);
        $this->crud->addButton('line', 'verify', 'view', 'dwfw::crud.buttons.users.verify', 'beginning');

        $this->setupColumnsFieldsFromMethod();
        $this->setupFiltersFromMethod();
        if (config('dwfw.user_list_global', true)) {
            User::withoutGlobalScope(AccountScope::class)->get();
        }
    }

    public function show($id)
    {
        if (view()->exists('admin.users.show')) {
            $this->crud->hasAccessOrFail('list');
            $user = User::find($id);
            $this->data['crud'] = $this->crud;
            $this->data['user'] = $user;
            return view('admin.users.show', $this->data);
        } else {
            return $this->traitShow($id);
        }
    }

    public function fetchUser()
    {
        return $this->fetch([
            'model' => User::class,
            'searchable_attributes' => ['name', 'email'],
            'paginate' => 10,
        ]);
    }

    public function store()
    {
        $this->handleFileUpload('profile_image', null, 'users');
        $this->crud->setValidation(UserStoreRequest::class);
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        return $this->traitStore();
    }

    public function update()
    {
        $this->handleFileUpload('profile_image', null, 'users');
        $this->crud->setValidation(UserUpdateRequest::class);
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        return $this->traitUpdate();
    }

    //<editor-fold desc="coluns/fields/filter" defaultstate="collapsed">
    public function getColumns()
    {
        return [
            [
                'name' => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type' => 'email',
            ],
            [ // n-n relationship (with pivot table)
                'label' => trans('backpack::permissionmanager.roles'), // Table column heading
                'type' => 'select_multiple',
                'name' => 'roles', // the method that defines the relationship in your Model
                'entity' => 'roles', // the method that defines the relationship in your Model
                'attribute' => 'display_name', // foreign key attribute that is shown to user
                'model' => config('permission.models.role'), // foreign key model
            ],
            [ // n-n relationship (with pivot table)
                'label' => trans('backpack::permissionmanager.extra_permissions'), // Table column heading
                'type' => 'select_multiple',
                'name' => 'permissions', // the method that defines the relationship in your Model
                'entity' => 'permissions', // the method that defines the relationship in your Model
                'attribute' => 'display_name', // foreign key attribute that is shown to user
                'model' => config('permission.models.permission'), // foreign key model
            ],
            [
                'name' => 'partner',
                'label' => __('dwfw::partners.partner'),
                'type' => 'select',
                'entity' => 'partner',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('partner', function ($q) use ($column, $searchTerm) {
                        $q->where('name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('contact_name', 'like', '%' . $searchTerm . '%');
                    });
                },
            ],
            [
                'name' => 'email_verified_at',
                'label' => __('dwfw::users.verified_at'),
                'type' => 'date',
            ],

        ];
    }

    protected function getFields()
    {
        return [
            [
                'name' => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type' => 'email',
            ],
            [
                'name' => 'accounts',
                'label' => __('dwfw::accounts.accounts'),
                'type' => 'select2_multiple',
            ],
            [
                'name' => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type' => 'password',
            ],
            [
                'name' => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type' => 'password',
            ],
            [
//                // two interconnected entities
                'label' => trans('backpack::permissionmanager.user_role_permission'),
                'field_unique_name' => 'user_role_permission',
                'type' => 'checklist_dependency',
                'name' => ['roles', 'permissions'],
                'primary_query' => function ($query) {
                    if (!backpack_user()->hasRole('super admin')) {
                        $query->where('name', '<>', 'super admin');
                    }
                },
                'subfields' => [
                    'primary' => [
                        'label' => trans('backpack::permissionmanager.roles'),
                        'name' => 'roles', // the method that defines the relationship in your Model
                        'entity' => 'roles', // the method that defines the relationship in your Model
                        'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
                        'attribute' => 'display_name', // foreign key attribute that is shown to user
                        'model' => config('permission.models.role'), // foreign key model
                        'pivot' => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns' => 3, //can be 1,2,3,4,6
                    ],
                    'secondary' => [
                        'label' => ucfirst(trans('backpack::permissionmanager.permission_singular')),
                        'name' => 'permissions', // the method that defines the relationship in your Model
                        'entity' => 'permissions', // the method that defines the relationship in your Model
                        'entity_primary' => 'roles', // the method that defines the relationship in your Model
                        'attribute' => 'display_name', // foreign key attribute that is shown to user
                        'model' => config('permission.models.permission'), // foreign key model
                        'pivot' => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns' => 3, //can be 1,2,3,4,6
                    ],
                ],
            ],
            [
                'name' => 'separator',
                'type' => 'custom_html',
                'value' => '<a target="blank" href="' . backpack_url('permission') . '"><i class="fas fa-info-circle"></i> ' . __('backpack::permissionmanager.permission_descriptions') . '</a>'
            ],
            [
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
            ],

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
                'type' => 'image',
                'upload' => true,
                'wrapper' => [
                    'class' => 'form-group col-12 col-sm-6',
                ],
            ],
        ];
    }

    protected function getFilters()
    {
        return [
            [
                [
                    'name' => 'name',
                    'type' => 'text',
                    'label' => __('dwfw::users.name'),
                ],
                false,
                function ($value) {
                    $this->crud->addClause('where', 'name', 'like', '%' . $value . '%');
                },
            ],
            [
                [
                    'name' => 'email',
                    'type' => 'text',
                    'label' => __('dwfw::users.email'),
                ],
                false,
                function ($value) {
                    $this->crud->addClause('where', 'email', 'like', '%' . $value . '%');
                },
            ],
            [
                [
                    'name' => 'role',
                    'type' => 'select2_multiple',
                    'label' => __('dwfw::users.roles'),
                ],
                function () {
                    return Role::all()->pluck('display_name', 'id')->toArray();
                },
                function ($values) {
                    $values = explode(',', preg_replace('/[^\d|,]/', '', $values));
                    $this->crud->query->whereHas('roles', function (Builder $query) use ($values) {
                        $query->wherein('id', $values);
                    });
                }
            ],
        ];
    }
    //</editor-fold>

    /*
    |--------------------------------------------------------------------------
    | CUSTOM NON-BACKPACK METHODS
    |--------------------------------------------------------------------------
    */
    /**
     * Handle password input fields.
     */
    protected function handlePasswordInput($request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');
        $request->request->remove('roles_show');
        $request->request->remove('permissions_show');

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', Hash::make($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        return $request;
    }

    public function changeAccount(AccountChangeRequest $request)
    {
        $selected_id = $request->validated()['account_id'];
        $user = auth()->user();
        if (
            ($user->hasPermissionTo('change account') || $user->hasRole('super admin') || in_array($selected_id, session('account_ids'))) &&
            (-1 == $selected_id || null != Account::query()->find($selected_id))
        ) {
            session(['account_id' => $selected_id]);
        }
        return back();
    }

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
