<?php


namespace Different\Dwfw\app\Models;

use Different\Dwfw\app\Models\Traits\BaseDwfwTrait;
use Different\Dwfw\app\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use BaseDwfwTrait;
    use CacheQueryBuilder;
}
