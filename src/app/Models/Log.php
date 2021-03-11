<?php

namespace Different\Dwfw\app\Models;

use App\Models\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\Settings\app\Models\Setting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Request;

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
    const ET_SETTING = Setting::class;
    const ET_PARTNER = Partner::class;
    const ET_USER = User::class;
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
    public static function getCountForEventsByTreshold(array $log_events, $treshold)
    {
        return self::query()
            ->where(function ($query) use ($log_events) {
                foreach ($log_events as $log_event) {
                    $query->orWhere('event', $log_event);
                }
            })
            ->whereIpAddress(Request::ip())
            ->where('created_at', '>=', Carbon::now()->subMinutes($treshold)->toDateTimeString())
            ->count();
    }


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
        if (!class_exists($this->entity_type)) {
            return $this->entity_type;
        }
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

    public function getLogIdAttribute()
    {
        return $this->entity_id;
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
