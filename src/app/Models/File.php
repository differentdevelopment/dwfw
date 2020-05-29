<?php

namespace Different\Dwfw\app\Models;

use Different\Dwfw\app\Models\Partner;
use Carbon\Carbon;
use Different\Dwfw\app\Http\Controllers\Files;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

/**
 * Class File
 * @package Different\Dwfw\app\Models
 * @property int $id
 * @property int $partner_id
 * @property Partner $partner
 * @property string $original_name
 * @property string $mime_type
 * @property string $file_path
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class File extends Model
{

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'files';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'partner_id',
        'original_name',
        'mime_type',
        'file_path',
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

    /**
     * @param string|null $storage_path
     */
    public function resizeImage(?string $storage_path = 'app/' . Files::STORAGE_DIR): void
    {
        $img = Image::make(storage_path($storage_path) . $this->file_path)->resize(1500, 1500, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save(storage_path($storage_path) . $this->file_path);
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
