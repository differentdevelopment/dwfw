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
class File extends BaseModel
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
        'access_hash',
    ];
    // protected $hidden = [];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $default_attributes = [
        'resize_x' => 1500,
        'resize_y' => 1500,
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'access_hash';
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /**
     * @param string|null $storage_path
     */
    public function orientate(?string $storage_path = 'app/' . Files::STORAGE_DIR): void
    {
        $image_path = $this->getImagePath($storage_path);
        ini_set('memory_limit','256M');
        Image::make($image_path)
            ->orientate()
            ->save($image_path);
    }

    /**
     * A PHP a 8.0 verziótól képes lett kezelni a nevesített paramétereket, így onnantól lehet hívni csak attribútumra. Előtte sajnos meg kell majd adni a storage_pathot..
     * @param null|string $storage_path
     * @param array $attributes
     */
    public function resizeImage(?string $storage_path = 'app/' . Files::STORAGE_DIR, array $attributes = []): void
    {
        $attributes = array_merge($this->default_attributes, $attributes);
        $image_path = $this->getImagePath($storage_path);
        ini_set('memory_limit','256M');
        Image::make($image_path)
            ->resize($attributes['resize_x'], $attributes['resize_y'], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->save($image_path);
    }

    public function identifiableAttribute()
    {
        return 'original_name';
    }

    /**
     * @param string|null $storage_path
     * @return string
     */
    protected function getImagePath(?string $storage_path): string
    {
        return storage_path($storage_path . $this->file_path);
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
