<?php

namespace Different\Dwfw\app\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Different\Dwfw\app\Models\Log;
use App\Models\User;

class LogsCrudController extends BaseCrudController
{
    use ListOperation;
    use ShowOperation;

    public function setup()
    {
        $this->crud->setModel(Log::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/logs');
        $this->crud->setEntityNameStrings(__('dwfw::logs.log'), __('dwfw::logs.logs'));

        if (!$this->crud->getRequest()->order) {
            $this->crud->orderBy('created_at', 'desc');
        }

        $this->setupColumnsFieldsFromMethod();
    }

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);
        $this->crud->addColumn([
            'name' => 'data',
            'label' => 'Data',
            'type' => 'closure',
            'function' => function ($entry) {
                return '<pre>' . json_encode(json_decode(utf8_decode($entry->data)), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            }
        ])->afterColumn('created_at');
        $this->crud->addColumn([
            'name' => 'ip_address',
            'label' => 'Ip address',
            'type' => 'text'
        ])->afterColumn('data');
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
                'name' => 'user_id',
                'label' => __('dwfw::logs.user_id'),
                'type' => 'closure',
                'function' => function ($entry) {
                    try {
                        if (!function_exists('getUserModelByRoute') || !($model = getUserModelByRoute($entry->route))) {
                            $model = new User;
                        }
                        return $model->findOrFail($entry->user_id)->name;
                    } catch (\Exception $e) {
                        return '';
                    }
                }
            ],
            [
                'name' => 'route',
                'label' => __('dwfw::logs.route'),
                'type' => 'text',
            ],
            [
                'name' => 'entity_type',
                'label' => __('dwfw::logs.entity_type'),
                'type' => 'text',
            ],
            [
                'name' => 'entity_id',
                'label' => __('dwfw::logs.entity_id'),
                'type' => 'text',
            ],
            [
                'name' => 'event',
                'label' => __('dwfw::logs.event'),
                'type' => 'text',
            ],
            [
                'name' => 'created_at',
                'label' => __('dwfw::logs.created_at'),
                'type' => 'datetime',
            ],
        ];
    }

    protected function getFields()
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
