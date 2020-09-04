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
        $this->setupFiltersFromMethod();
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

    protected function getFilters()
    {
        return
            [
                [
                    [
                        'name' => 'user_id',
                        'type' => 'select2',
                        'label' => __('dwfw::logs.user_id'),
                    ],
                    function () {
                        return User::all()->pluck('name', 'id')->toArray();
                    },
                    function ($value) {
                        $this->crud->addClause('where', 'user_id', '=', $value);
                    },
                ],

                [
                    [
                        'name' => 'route',
                        'type' => 'select2',
                        'label' => __('dwfw::logs.route'),
                    ],
                    function () {
                        return Log::query()->pluck('route', 'route')->toArray(); //Needs to be key => value, even when they're the same
                    },
                    function ($value) {
                        $this->crud->addClause('where', 'route', '=', $value);
                    },
                ],

                [
                    [
                        'name' => 'entity_type',
                        'type' => 'select2_multiple',
                        'label' => __('dwfw::logs.entity_type'),
                    ],
                    function () {
                        return Log::query()->pluck('entity_type', 'entity_type')->toArray(); //Needs to be key => value, even when they're the same
                    },
                    function ($values) {
                        $this->crud->addClause('whereIn', 'entity_type', json_decode($values));
                    },
                ],
                [
                    [
                        'name' => 'entity_id',
                        'type' => 'select2',
                        'label' => __('dwfw::logs.entity_id'),
                    ],
                    function () {
                        return Log::query()->pluck('entity_id', 'entity_id')->toARray();
                    },
                    function ($value) {
                        $this->crud->addClause('where', 'entity_id', $value);
                    },
                ],

                [
                    [
                        'name' => 'event',
                        'type' => 'select2_multiple',
                        'label' => __('dwfw::logs.event'),
                    ],
                    function () {
                        return Log::query()->pluck('event', 'event')->toARray();
                    },
                    function ($values) {
                        $this->crud->addClause('whereIn', 'event', json_decode($values));
                    },
                ],

                [
                    [
                      'name' => 'created_at',
                      'type' => 'date_range',
                      'label' => __('dwfw::logs.created_at')
                    ],
                    false,
                    function ($value) {
                        $dates = json_decode($value);
                        $this->crud->addClause('where', 'created_at', '>=', $dates->from);
                        $this->crud->addClause('where', 'created_at', '<=', $dates->to.' 23:59:59');
                    },
                ]
            ];
    }
    //</editor-fold>

    /*
   |--------------------------------------------------------------------------
   | CUSTOM NON-BACKPACK METHODS
   |--------------------------------------------------------------------------
   */
}
