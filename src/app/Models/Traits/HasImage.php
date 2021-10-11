<?php

namespace Different\Dwfw\app\Models\Traits;

use Different\Dwfw\app\Http\Controllers\Files;
use Different\Dwfw\app\Models\File;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasImage
{

    protected string $default_image;

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->default_image = asset('images/image-not-found.png');
    }

    public function addImage(?string $base64_string = null, string $column_name = 'image'): File|null
    {
        if (!$base64_string) {
            $this->deleteFile($column_name);
            return null;
        }

        $image = Files::storeBase64(
            $base64_string,
            null,
            method_exists($this, 'getImageFolderAttribute') ? $this->image_folder : self::IMAGE_STORAGE_DIR,
            'files',
            null,
            $this->{$column_name . '_id'},
        );
        $this->{$column_name . '_id'} = $image->id;
        $this->save();

        return $image;
    }

    public function deleteFile(string $column_name = 'image'): self
    {
        if ($this->{$column_name . '_id'}) Files::delete($this->{'file_' . $column_name});

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function file_image(?string $column_name = 'image'): BelongsTo
    {
        return $this->belongsTo(File::class, $column_name . '_id');
    }

    /*
   |--------------------------------------------------------------------------
   | MUTATORS
   |--------------------------------------------------------------------------
   */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getImageAttribute(?string $column_name): ?string
    {
        if (!$column_name) $column_name = 'image'; // default paraméter nem megy

        if (!$this->{'file_' . $column_name}) {
            if (defined('self::WITHOUT_DEFAULT_IMAGE') && self::WITHOUT_DEFAULT_IMAGE) return null;
            return $this->default_image;
        }

        return route('file', ['files', $this->{'file_' . $column_name}]);
    }

}
