<?php

namespace Different\Dwfw\app\Models;

use Different\Dwfw\app\Extensions\DwfwCollection;
use Different\Dwfw\app\Models\Traits\BaseDwfwTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use BaseDwfwTrait;

    public function newCollection(array $models = array())
    {
        return new DwfwCollection($models);
    }
}
