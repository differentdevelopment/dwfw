<?php

namespace Different\Dwfw\app\Http\Controllers;

use Different\Dwfw\app\Http\Requests\PartnerRequest;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Different\Dwfw\app\Models\Partner;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

/**
 * Class PartnerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class PartnersCrudController extends BaseCrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;
    use SoftDeletes;

    public function setup()
    {
        $this->crud->setModel(Partner::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/partners');
        $this->crud->setEntityNameStrings(__('dwfw::partners.partners'), __('dwfw::partners.partners'));

        if (!$this->crud->getRequest()->order) {
            $this->crud->orderBy('name', 'asc');
        }

        $this->setupColumnsFieldsFromMethod();
        $this->crud->setValidation(PartnerRequest::class);
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
                'name' => 'name',
                'label' => __('dwfw::partners.name'),
                'type' => 'text',
            ],
            [
                'name' => 'contact_name',
                'label' => __('dwfw::partners.contact_name'),
                'type' => 'text',
            ],
            [
                'name' => 'contact_phone',
                'label' => __('dwfw::partners.contact_phone'),
                'type' => 'text',
            ],
            [
                'name' => 'contact_email',
                'label' => __('dwfw::partners.contact_email'),
                'type' => 'email',
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
                'label' => __('dwfw::partners.name'),
                'type' => 'text',
            ],
            [
                'name' => 'contact_name',
                'label' => __('dwfw::partners.contact_name'),
                'type' => 'text',
            ],
            [
                'name' => 'contact_phone',
                'label' => __('dwfw::partners.contact_phone'),
                'type' => 'text',
            ],
            [
                'name' => 'contact_email',
                'label' => __('dwfw::partners.contact_email'),
                'type' => 'email',
            ],
        ];
    }

    //</editor-fold>

    public function ajaxList(Request $request)
    {
        return Partner::query()
            ->where('name', 'like', '%' . $request->input('term') . '%')
            ->orWhere('contact_name', 'like', '%' . $request->input('term') . '%')
            ->get()
            ->pluck('name_contact_name', 'id');
    }
    /*
    |--------------------------------------------------------------------------
    | CUSTOM NON-BACKPACK METHODS
    |--------------------------------------------------------------------------
    */

}
