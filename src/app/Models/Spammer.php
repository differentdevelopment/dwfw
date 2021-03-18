<?php

namespace Different\Dwfw\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;

/**
 * Class Partner
 * @package App\Models
 * @property int $id
 * @property string $ip_address
 * @property int $attempts
 * @property Carbon $blocked_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Spammer extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'spammers';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $hidden = [];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
