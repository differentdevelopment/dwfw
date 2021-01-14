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
        if ($this->isBase64Image($this->crud->getRequest()->{$input_name})) {
            $file = Files::storeBase64($this->crud->getRequest()->{$input_name}, $partner_id, $storage_dir);
            $this->addColumnIdToRequest($input_name, $file->id);
        } elseif ($this->crud->getRequest()->hasFile($input_name)) {
            $file = Files::store($this->crud->getRequest()->{$input_name}, $partner_id, $storage_dir);
            $this->addColumnIdToRequest($input_name, $file->id);
        } elseif ($this->crud->getRequest()->has($input_name) && $this->crud->getRequest()->$input_name == null) {
            $this->addColumnIdToRequest($input_name, '');
            $this->crud->getRequest()->request->remove($input_name);
        }
    }

    /**
     * @param string $value
     * @return bool
     */
    private function isBase64Image($value) {
        $explode = explode(',', $value);
        $allow = ['png', 'jpg', 'jpeg', 'svg', 'gif'];
        $format = str_replace(
            [
                'data:image/',
                ';',
                'base64',
            ],
            [
                '', '', '',
            ],
            $explode[0]
        );
        if (!in_array($format, $allow)) {
            return false;
        }
        if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
            return false;
        }
        return true;
    }

}
