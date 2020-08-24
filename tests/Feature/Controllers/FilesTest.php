<?php

namespace Different\Dwfw\Tests\Unit\Models;

use Different\Dwfw\app\Http\Controllers\Files;
use Different\Dwfw\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FilesTest extends TestCase
{
    /**
     * @test
     */
    function uploaded_file_exists()
    {
        Storage::fake('photos');
        $fake_file = Files::store(UploadedFile::fake()->image('photo1.jpg'), null, null, 'photos');
        Storage::disk('photos')->assertExists($fake_file->file_path);
    }

    /**
     * @test
     */
    function uploaded_file_exists_in_storage_dir()
    {
        Storage::fake('photos');
        $fake_file = Files::store(UploadedFile::fake()->image('photo1.jpg'), null, 'foo', 'photos');
        Storage::disk('photos')->assertExists('foo/' . $fake_file->file_path);
    }

    /**
     * @test
     */
    function uploaded_file_exists_in_partners_dir()
    {
        Storage::fake('photos');
        $partner = $this->createPartner();
        $fake_file = Files::store(UploadedFile::fake()->image('photo1.jpg'), $partner->id, null, 'photos');
        Storage::disk('photos')->assertExists($partner->id . '/' . $fake_file->file_path);
    }
}
