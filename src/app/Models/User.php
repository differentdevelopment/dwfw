<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Creativeorange\Gravatar\Facades\Gravatar;
use Different\Dwfw\app\Models\Partner;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

/**
 * Class User
 * @package App\Models
 * @property string name
 * @property string email
 * @property Carbon email_verified_at
 * @property string password
 * @property int partner_id
 * @property Partner $partner
 * @property int timezone_id
 * @property TimeZone $timezone
 * @property string $remember_token
 * @property int $profile_image_id
 * @property File $profile_image
 * @property string last_device
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles;
    use CrudTrait;
    use Notifiable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $guard_name = 'web';

    protected $table = 'users';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'email',
        'password',
        'partner_id',
        'timezone_id',
        'last_device',
        'profile_image_id',
        'email_verified_at',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
    ];
    protected $casts = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function getProfileImage()
    {
        if ($this->profile_image_id) {
            return route('image', $this->profile_image);
        } else {
            return Gravatar::fallback('https://placehold.it/160x160/662d8c/b284d1/&text=' . strtoupper(substr($this->email, 0, 1)))->get($this->email);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function partner()
    {
        return $this->belongsTo( Partner::class);
    }

    public function profile_image()
    {
        return $this->belongsTo(File::class)->whereNull('partner_id');
    }

    public function user_tokens()
    {
        return $this->hasMany(UserToken::class);
    }

    public function timezone()
    {
        return $this->belongsTo(TimeZone::class);
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
