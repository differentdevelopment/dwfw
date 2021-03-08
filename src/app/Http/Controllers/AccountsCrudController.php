<?php

namespace Different\Dwfw\app\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\Settings\app\Models\Setting;
use Different\Dwfw\app\Http\Controllers\Traits\ColumnFaker;
use Different\Dwfw\app\Http\Controllers\Traits\FileUpload;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Different\Dwfw\app\Http\Requests\AccountRequest;
use Different\Dwfw\app\Models\Account;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Class PartnerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class AccountsCrudController extends BaseCrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;
    use SoftDeletes;

    public function setup()
    {
        $this->crud->setModel(config('dwfw.account_model', Account::class));
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/accounts');
        $this->crud->setEntityNameStrings(__('dwfw::accounts.account'), __('dwfw::accounts.accounts'));

        if (!$this->crud->getRequest()->order) {
            $this->crud->orderBy('name', 'asc');
        }
        $this->setupColumnsFieldsFromMethod();
        $this->crud->setValidation(AccountRequest::class);
    }

    //<editor-fold desc="columns/fields" defaultstate="collapsed">

    public function ajaxList(Request $request)
    {
        return Partner::query()
            ->where('name', 'like', '%' . $request->input('term') . '%')
            ->get()
            ->pluck('name', 'id');
    }

    /**
     * returns the detailed columns array for the grid
     * @return array columns array
     */
    protected function getColumns()
    {
        return [
            [
              'name' => 'id',
              'label' => 'ID',
              'type' => 'text',
            ],
            [
                'name' => 'name',
                'label' => 'Név',
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
                'name' => 'name',
                'label' => __('dwfw::accounts.name'),
                'type' => 'text',
            ],
        ];
    }

    protected function getFilters()
    {
        return [];
    }

    //</editor-fold>

    /*
    |--------------------------------------------------------------------------
    | CUSTOM NON-BACKPACK METHODS
    |--------------------------------------------------------------------------
    */

}
