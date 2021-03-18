<?php

namespace App\Models;

//use App\Notifications\CustomPasswordResetNotification;
//use App\Notifications\CustomRegistrationConfirmNotification;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Different\Dwfw\app\Models\File;
use Different\Dwfw\app\Models\Partner;
use Different\Dwfw\app\Models\TimeZone;
use Different\Dwfw\app\Models\Traits\DwfwUser;
use Different\Dwfw\database\factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon $email_verified_at
 * @property string $password
 * @property int $partner_id
 * @property Partner $partner
 * @property int $timezone_id
 * @property TimeZone $timezone
 * @property string $remember_token
 * @property int $profile_image_id
 * @property File $profile_image
 * @property string $last_device
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use CrudTrait;
    use DwfwUser;
    use HasFactory;
    use HasRoles;
    use Notifiable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
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

    private $default_image;
    private $default_image_icon;

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->default_image = asset('images/assets/dwfw-show.png');
        $this->default_image_icon = asset('images/assets/dwfw-show-icon.png');
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function logName()
    {
        return 'name';
    }

//    Remove comment if you've installed passport via Dwfw

//    public function sendEmailVerificationNotification()
//    {
//        $this->notify(new CustomRegistrationConfirmNotification);
//    }
//
//    public function sendPasswordResetNotification($token)
//    {
//        $this->notify(new CustomPasswordResetNotification($token));
//    }

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
