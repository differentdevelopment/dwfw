<?php

namespace Different\Dwfw\Tests\Unit\Models;

use Different\Dwfw\app\Http\Controllers\Files;
use Different\Dwfw\app\Models\Partner;
use Different\Dwfw\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FilesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }


    /**
     * @test
     */
    function uploaded_file_exists()
    {
        Storage::fake('photos');

        $fake_file = Files::store(UploadedFile::fake()->image('photo1.jpg'), null,  null, 'photos');

        Storage::disk('photos')->assertExists($fake_file->file_path);

    }
}
