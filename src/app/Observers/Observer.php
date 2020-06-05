<?php

namespace Different\Dwfw\app\Observers;

use Different\Dwfw\app\Models\Log;
use Different\Dwfw\app\Traits\LoggableAdmin;
use Illuminate\Database\Eloquent\Model;

abstract class Observer
{
    const DATA_SEPARATOR = ' | ';
    use LoggableAdmin;

    protected function implodeData(array $data_array)
    {
        return trim(array_reduce($data_array, fn($carry, $data) => $data ? ($carry .= $data . self::DATA_SEPARATOR) : $carry), self::DATA_SEPARATOR);
    }

    public function created($object)
    {
        $this->log(Log::E_CREATED, $object->id, $this->getData($object));
    }

    public function updated($object)
    {
        $this->log(Log::E_UPDATED, $object->id, $this->getData($object));
    }

    public function deleted($object)
    {
        $this->log(Log::E_DELETED, $object->id, $this->getData($object));
    }

    /**
     * return the data depending on current object
     * @param Model $object
     */
    public abstract function getData(Model $object);
}
