<?php

namespace Different\Dwfw\app\Http\Controllers;

use Backpack\Settings\app\Http\Controllers\SettingCrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SettingController extends SettingCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel("Backpack\Settings\app\Models\Setting");
        CRUD::setEntityNameStrings(trans('backpack::settings.setting_singular'), trans('backpack::settings.setting_plural'));
        CRUD::setRoute(backpack_url('setting'));
    }

    public function setupListOperation()
    {        
        // only show settings which are marked as active
        CRUD::addClause('where', 'active', 1);

        // columns to show in the table view
        CRUD::setColumns([
            [
                'name'  => 'name',
                'label' => trans('backpack::settings.name'),                
                'limit' => 2000,
                'wrapper'   => [                
                    'style' => ' display: block; 
                    width: 500px; 
                    height: auto;
                    word-break: break-all;
                    word-wrap: break-word;
                    white-space: normal;'
                    ],
                             
               
            ],
            [
                'name'  => 'value',
                'label' => trans('backpack::settings.value'),
                'limit' => 2000,
                'wrapper'   => [                
                    'style' => ' display: block; 
                    width: 300px; 
                    height: auto;
                    word-break: break-all;
                    word-wrap: break-word;
                    white-space: normal;'
                    ],
                
            ],
            [
                'name'  => 'description',
                'label' => trans('backpack::settings.description'),
                'limit' => 2000,
                'wrapper'   => [                
                    'style' => ' display: block; 
                    width: 500px; 
                    height: auto;
                    word-break: break-all;
                    word-wrap: break-word;
                    white-space: normal;'
                    ],
                
            ],
        ]);
        
    }    
    public function setupUpdateOperation()
    {
        CRUD::addField([
            'name'       => 'name',
            'label'      => trans('backpack::settings.name'),
            'type'       => 'text',
            'attributes' => [
                'disabled' => 'disabled',
            ],
        ]);

        CRUD::addField(json_decode(CRUD::getCurrentEntry()->field, true));
    }
}
?>