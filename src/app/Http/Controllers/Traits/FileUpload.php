<?php


namespace Different\Dwfw\app\Http\Controllers\Traits;

use Different\Dwfw\app\Http\Controllers\Files;

trait FileUpload
{

    /**
     * if {$input_name} file is set, stores the file, adds {$input_name}_id to grid/request
     * @param string $input_name
     * @param int $partner_id
     * @param string|null $storage_dir
     */
    protected function handleFileUpload(string $input_name, ?int $partner_id = null, ?string $storage_dir = null)
    {
        if ($this->crud->getRequest()->hasFile($input_name)) {
            $file = Files::store($this->crud->getRequest()->{$input_name}, $partner_id, $storage_dir);
            $this->addColumnIdToRequest($input_name, $file->id);
        } elseif ($this->crud->getRequest()->has($input_name) && $this->crud->getRequest()->$input_name == null) {
            $this->addColumnIdToRequest($input_name, '');
            $this->crud->getRequest()->request->remove($input_name);
        }
    }

}
