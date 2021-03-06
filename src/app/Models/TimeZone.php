<?php

namespace Different\Dwfw\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Exception;

/**
 * Class TimeZone
 * @package Different\Dwfw\app\Models
 * @property string $name
 * @property string $diff
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class TimeZone extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'timezones';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'diff',
    ];
    // protected $hidden = [];
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    public const DEFAULT_TIMEZONE_CODE = 326;

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /**
     * returns first Timezone object with $diff && $continent
     * @param string $timezone_diff
     * @param string|null $continent_prefix
     * @return TimeZone|null
     */
    public static function getTimezoneByDiff(string $timezone_diff, ?string $continent_prefix = null): ?TimeZone
    {
        $timezone = self::query()->where('diff', $timezone_diff);
        if ($continent_prefix) {
            $timezone->where('name', 'LIKE', $continent_prefix . '/%');
        }
        try {
            return $timezone->first();
        } catch (Exception $e) {
            return null;
        }
    }

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
    public function getNameWithDiffAttribute()
    {
        return $this->name . ' (UTC' . $this->diff . ')';
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
