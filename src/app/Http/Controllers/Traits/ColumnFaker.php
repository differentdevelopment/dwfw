<?php

namespace Different\Dwfw\app\Http\Controllers\Traits;

trait ColumnFaker
{
    /**
     * adds {$column}_id with value to the grid/request before storing data
     * @param string $column The name of the foreign key column, without the "_id" suffix
     * @param null $value
     */
    protected function addColumnIdToRequest(string $column, $value = null): void
    {
        if ($value === null && isset($this->{$column . '_id'})) {
            $value = $this->{$column . '_id'};
        } elseif ($value === '') {
            $value = null;
        } elseif ($value === null) {
            return;
        }
        $this->crud->addField(['name' => $column . '_id', 'type' => 'hidden']);
        $this->crud->getRequest()->request->add([$column . '_id' => $value]);
    }

}
