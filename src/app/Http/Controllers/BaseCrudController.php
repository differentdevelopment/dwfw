<?php

namespace Different\Dwfw\app\Http\Controllers;

use Different\Dwfw\app\Http\Controllers\Traits\ColumnFaker;
use Different\Dwfw\app\Http\Controllers\Traits\FileUpload;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Support\Facades\Route;

abstract class BaseCrudController extends CrudController
{
    use FileUpload;
    use ColumnFaker;

    protected function setupColumnsFieldsFromMethod(): void
    {
        $this->crud->setColumns($this->getColumns());
        $this->crud->addFields($this->getFields());
    }

    protected function setupFiltersFromMethod(): void
    {
        foreach($this->getFilters() as $filter) {
            $this->crud->addFilter($filter[0], $filter[1], $filter[2]);
        }
    }

    abstract protected function getColumns();

    abstract protected function getFields();

    abstract protected function getFilters();

    /**
     * Checks for {$column}_id
     * @param string $column The name of the foreign key column, without the "_id" suffix
     */
    protected function checkForColumnId(string $column): void
    {
        if ('partner' == $column) {
            $this->{$column . '_id'} = backpack_user()->hasRole('partner') ? backpack_user()->{$column . '_id'} : Route::current()->parameter($column . '_id');
        } else {
            $this->{$column . '_id'} = Route::current()->parameter($column . '_id');
        }
        if ($this->{$column . '_id'}) {
            $model_name = 'App\Models\\' . ucfirst($column);
            $model = new $model_name;
            $this->{$column} = $model::findOrFail($this->{$column . '_id'});
            $this->crud->setRoute($this->crud->getRoute() . '/' . $this->{$column . '_id'} . '/' . $column);
            $this->crud->setTitle($this->{$column}->crud_title ?? $this->{$column}->title ?? $this->{$column}->name ?? __('dwfw::dwfw.missing_crud_title'));
            $this->crud->setHeading($this->{$column}->crud_title ?? $this->{$column}->title ?? $this->{$column}->name ?? __('dwfw::dwfw.missing_crud_title'));

            $this->crud->addClause('where', $column . '_id', '=', $this->{$column . '_id'});
            $this->crud->removeField($column . '_id');
            $this->crud->removeColumn($column . '_id');
        }
    }

    protected function addPartnerFilter()
    {
        $this->crud->addFilter([
            'name' => 'partner_id',
            'type' => 'select2_ajax',
            'label' => __('dwfw::partners.partner'),
        ],
            route('admin.partners.ajax-partner-list'),
            function ($value) { // if the filter is active
                if ($value) {
                    $this->crud->addClause('where', 'partner_id', $value);
                }
            }
        );
    }

}
