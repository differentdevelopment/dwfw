<?php

namespace Different\Dwfw\app\Http\Controllers;

use Different\Dwfw\app\Http\Requests\PartnerRequest;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Different\Dwfw\app\Http\Requests\SpammerRequest;
use Different\Dwfw\app\Models\Partner;
use Different\Dwfw\app\Models\Spammer;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Spatie\Permission\Middlewares\PermissionMiddleware;

/**
 * Class PartnerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SpammersCrudController extends BaseCrudController
{
    use ListOperation;
    use CreateOperation;
    use DeleteOperation;
    use ShowOperation;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(PermissionMiddleware::class . ':manage bans');
    }

    public function setup()
    {
        $this->crud->setModel(Spammer::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/spammers');
        $this->crud->setEntityNameStrings(__('dwfw::spammers.spammer'), __('dwfw::spammers.spammers'));

        if (!$this->crud->getRequest()->order) {
            $this->crud->orderBy('created_at', 'desc');
        }

        $this->setupColumnsFieldsFromMethod();
        $this->setupFiltersFromMethod();
        $this->crud->setValidation(SpammerRequest::class);
    }

    //<editor-fold desc="columns/fields" defaultstate="collapsed">

    /**
     * returns the detailed columns array for the grid
     * @return array columns array
     */
    protected function getColumns()
    {
        return [
            [
                'name' => 'ip_address',
                'label' => __('dwfw::spammers.ip_address'),
                'type' => 'text',
            ],
            [
                'name' => 'attempts',
                'label' => __('dwfw::spammers.attempts'),
                'type' => 'text',
            ],
            [
                'name' => 'blocked_at',
                'label' => __('dwfw::spammers.blocked_at'),
                'type' => 'datetime',
            ],
            [
                'name' => 'created_at',
                'label' => __('dwfw::spammers.created_at'),
                'type' => 'text',
            ],
        ];
    }

    /**
     * returns the detailed fields array for the grid
     * @return array fields araray
     */
    protected function getFields(): array
    {
        return [
            [
                'name' => 'ip_address',
                'label' => __('dwfw::spammers.ip_address'),
                'type' => 'text',
            ],
            [
                'name' => 'attempts',
                'label' => __('dwfw::spammers.attempts'),
                'type' => 'text',
            ],
            [
                'name' => 'blocked_at',
                'label' => __('dwfw::spammers.blocked_at'),
                'type' => 'datetime',
            ],
        ];
    }

    //</editor-fold>

    protected function getFilters()
    {
        return [
            [
                [
                    'type' => 'simple',
                    'name' => 'is_blocked',
                    'label' => __('dwfw::spammers.is_blocked'),
                ],
                false,
                function () { // if the filter is active
                    $this->crud->addClause('whereNotNull', 'blocked_at');
                },
            ],
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | CUSTOM NON-BACKPACK METHODS
    |--------------------------------------------------------------------------
    */

}
