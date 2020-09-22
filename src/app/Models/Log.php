<?php

namespace Different\Dwfw\app\Models;

use App\Models\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Log
 * @package Different\Dwfw\app\Models
 * @property int $id
 * @property int $user_id
 * @property User $user
 * @property string $route
 * @property string $entity_type
 * @property int $entity_id
 * @property string $event
 * @property string $data
 * @property string $ip_address
 */
class Log extends BaseModel
{
    use CrudTrait;

    const ET_SYSTEM = 'SYSTEM';
    const ET_OPERATION = 'OPERATION';
    const ET_CLOTH_MODEL = 'CLOTH_MODEL';
    const ET_SETTING = 'SETTING';
    const ET_PARTNER = 'PARTNER';
    const ET_USER = 'USER';
    const ET_AUTH = 'AUTH';
    const ET_PRODUCTION = 'PRODUCTION';
    const ET_PACKAGE = 'PACKAGE';

    const E_LOGIN = 'LOGIN';
    const E_FAIL = 'FAIL';
    const E_LOGOUT = 'LOGOUT';
    const E_CREATED = 'CREATED';
    const E_UPDATED = 'UPDATED';
    const E_DELETED = 'DELETE';

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'logs';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'user_id',
        'route',
        'entity_type',
        'entity_id',
        'event',
        'data',
        'ip_address',
        'status',
    ];
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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function entity()
    {
        return $this->morphTo();
    }

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
    public function getUserNameAttribute()
    {
        return $this->user->username;
    }

    public function getLogNameAttribute()
    {
        if (method_exists($this->entity_type, 'logName') && $this->entity) {
            return $this->entity->logName();
        } else {
            if ($this->entity) {
                return $this->entity->name ?? '-'; //Returns '-' if logName function doesn't exists in the given model and it also doesn't have name attribute
            } else {
                return __('dwfw::logs.deleted_entry');
            }
        }
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
