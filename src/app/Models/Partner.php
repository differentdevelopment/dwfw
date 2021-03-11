<?php

namespace Different\Dwfw\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Different\Dwfw\app\Models\Traits\DwfwPartner;
use Different\Dwfw\database\factories\PartnerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Partner
 * @package App\Models
 * @property string $name
 * @property string $short_name
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $contact_email
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class Partner extends BaseModel
{
    use CrudTrait;
    use DwfwPartner;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'partners';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'short_name',
        'contact_name',
        'contact_phone',
        'contact_email',
    ];
    // protected $hidden = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function newFactory()
    {
        return PartnerFactory::new();
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        // ez oldja meg, hogy a BaseModell global scope ne töltődjön be erre a modellre, ha később account függő lesz a tábla, akkor ezt az egész booted ki kell törölni innen
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
