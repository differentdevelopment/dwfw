<?php

namespace Different\Dwfw\app\Http\Controllers;

use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Different\Dwfw\app\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Middlewares\PermissionMiddleware;

class LogsCrudController extends BaseCrudController
{
    use ListOperation;
    use ShowOperation;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(PermissionMiddleware::class . ':view logs');
    }

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
    public function setupListOperation()
    {
        $this->crud->disableResponsiveTable();
    }

    protected function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    protected function setupShowOperation()
    {

        /*$this->crud->addColumn([
            'name' => 'data',
            'label' => 'Data',
            'type' => 'closure',
            'function' => function ($entry) {
                if($this->isJson($entry->data)){
                    return '<pre>' . json_encode(json_decode(utf8_decode($entry->data)), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
                }
                return '<pre>' . json_encode(utf8_decode($entry->data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            }
        ])->afterColumn('created_at');*/
        
        foreach($this->getFields() as $index => $farray)
        {
           $this->crud->addColumn($farray);
        }
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
                'name' => 'created_at',
                'label' => __('dwfw::logs.created_at'),
                'type' => 'datetime',
            ],
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
                },
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('name', 'like', '%' . $searchTerm . '%');
                    });
                },
            ],
            [
                'name' => 'user_email',
                'label' => __('dwfw::logs.user_email'),
                'type' => 'closure',
                'function' => function ($entry) {
                    try {
                        if (!function_exists('getUserModelByRoute') || !($model = getUserModelByRoute($entry->route))) {
                            $model = new User;
                        }
                        return $model->findOrFail($entry->user_id)->email;
                    } catch (\Exception $e) {
                        return '';
                    }
                },
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('email', 'like', '%' . $searchTerm . '%');
                    });
                },
            ],
            [
                'name' => 'event',
                'label' => __('dwfw::logs.event'),
                'type' => 'text',
            ],  
            [
                'name' => 'entity_type',
                'label' => __('dwfw::logs.entity_type'),
                'type' => 'text',
            ],         
            [
                'name' => 'ip_address',
                'label' => __('dwfw::logs.ip_address'),
                'type' => 'text',
            ],            
                       
        ];
    }

    protected function getFields()
    {
        return [
            [
                'name' => 'created_at',
                'label' => __('dwfw::logs.created_at'),
                'type' => 'datetime',
            ],
            [
                'name' => 'data',
                'label' => 'Data',
                'type' => 'closure',
                'function' => function ($entry) {
                    if($this->isJson($entry->data)){
                        return '<pre>' . json_encode(json_decode(utf8_decode($entry->data)), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
                    }
                    return '<pre>' . json_encode(utf8_decode($entry->data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
                }
            ],
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
                },
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('name', 'like', '%' . $searchTerm . '%');
                    });
                },
            ], 
            [
                'name' => 'user_email',
                'label' => __('dwfw::logs.user_email'),
                'type' => 'closure',
                'function' => function ($entry) {
                    try {
                        if (!function_exists('getUserModelByRoute') || !($model = getUserModelByRoute($entry->route))) {
                            $model = new User;
                        }
                        return $model->findOrFail($entry->user_id)->email;
                    } catch (\Exception $e) {
                        return '';
                    }
                },
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('email', 'like', '%' . $searchTerm . '%');
                    });
                },
            ],
            [
                'name' => 'event',
                'label' => __('dwfw::logs.event'),
                'type' => 'text',
            ], 
            [
                'name' => 'entity_type',
                'label' => __('dwfw::logs.entity_type'),
                'type' => 'text',
            ],    
            [
                'name' => 'ip_address',
                'label' => __('dwfw::logs.ip_address'),
                'type' => 'text',
            ],        
            [
                'name' => 'route',
                'label' => __('dwfw::logs.route'),
                'type' => 'text',
                'visibleInTable' => false,
            ],
            [
                'name' => 'log_id',
                'label' => __('dwfw::logs.entity_id'),
                'type' => 'text',
            ],  
            [
                'name' => 'status',
                'label' => __('dwfw::logs.status'),
            ],  
            [
                'name' => 'entity_name',
                'label' => __('dwfw::logs.entity_name'),
                'type' => 'closure',
                'function' => function ($entry) {
                    if ($entry->entity_id == null) {
                        return $entry->entity_type;
                    }
                    try {
                        return $entry->log_name;
                    } catch (\Exception $e) {
                        return '';
                    }
                },
            ],


        ];
    }

    protected function getFilters()
    {
        return
            [
                [
                    [
                        'name' => 'user_id',
                        'type' => 'select2_ajax_multiple',
                        'label' => __('dwfw::logs.user_id'),
                    ],
                    route('admin.ajax-user-options'),
                    function ($values) {
                        $this->crud->addClause('whereIn', 'user_id', json_decode($values));
                    },
                ],

                [
                    [
                        'name' => 'route',
                        'type' => 'select2',
                        'label' => __('dwfw::logs.route'),
                    ],
                    function () {
                        return Log::query()->groupBy('route')->pluck('route', 'route')->toArray(); //Needs to be key => value, even when they're the same
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
                        return Log::query()->groupBy('entity_type')->pluck('entity_type', 'entity_type')->toArray(); //Needs to be key => value, even when they're the same
                    },
                    function ($values) {
                        $this->crud->addClause('whereIn', 'entity_type', json_decode($values));
                    },
                ],
                [
                    [
                        'name' => 'entity_id',
                        'type' => 'text',
                        'label' => __('dwfw::logs.entity_id'),
                    ],
                    false,
                    function ($value) {
                        $this->crud->addClause('where', 'entity_id', $value);
                    },
                ],

                [
                    [
                        'name' => 'event',
                        'type' => 'select2',
                        'label' => __('dwfw::logs.event'),
                    ],
                    function () {
                        return Log::query()->groupBy('event')->pluck('event', 'event')->toArray(); //Needs to be key => value, even when they're the same
                    },
                    function ($value) {
                        $this->crud->addClause('where', 'event', '=', $value);
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
                        $dates = array_map(function ($element) {
                            $element = trim($element);
//                            $element .= '.000';
                            return substr($element, 0, 19);
                        }, (array)$dates);
                        $this->crud->addClause('where', 'created_at', '>=', $dates['from']);
                        $this->crud->addClause('where', 'created_at', '<=', $dates['to']);
                    },
                ],
                [
                    [
                        'type' => 'text',
                        'name' => 'ip_address',
                        'label' => __('dwfw::logs.ip_address'),
                    ],
                    false,
                    function ($value) {
                        $this->crud->addClause('where', 'ip_address', 'LIKE', "%$value%");
                    },
                ],
            ];
    }

    protected function userOptions(Request $request)
    {
        $term = $request->input('term');
        return User::query()->where('name', 'like', '%'.$term.'%')->get()->pluck('name', 'id');
    }

    //</editor-fold>

    /*
   |--------------------------------------------------------------------------
   | CUSTOM NON-BACKPACK METHODS
   |--------------------------------------------------------------------------
   */
}
