<?php

namespace Different\Dwfw\App\Models;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;

/**
 * Class File
 * @package App\Models
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
     * Stores file in storage and creates db entry
     * @param UploadedFile $file
     * @param Partner|int $partner
     * @return File|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public static function storeFile(UploadedFile $file, $partner = null): File
    {
        $partner_id = $partner === null ? null : ($partner instanceof Partner ? $partner->id : $partner);
        $path = $file->store($partner_id, 'uploads');
        return File::query()->create([
            'partner_id' => $partner_id,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_path' => $path,
        ]);
    }

    public function resizeImage()
    {
        $img = Image::make(storage_path() . '/app/public/uploads/' . $this->file_path)->resize(1500, 1500, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save(storage_path() . '/app/public/uploads/' . $this->file_path);
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
