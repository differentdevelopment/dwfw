<?php

namespace Different\Dwfw\app\Http\Controllers;

use Different\Dwfw\app\Models\Partner;
use Different\Dwfw\app\Models\File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class Files extends Controller
{

    public const STORAGE_DIR = 'files/';

    /**
     * FIXME automatic model binding does not work here, dunno why - alitak@20200525
     * @param int $file
     * @return \Illuminate\Http\Response
     * @throws FileNotFoundException
     */
    public function retrieve($file)
    {
        $file = File::query()->findOrFail($file);
        $file_path = storage_path('app/' . self::STORAGE_DIR . $file->file_path);

        if (app('files')->missing($file_path)) {
            abort(404);
        }

        return Response::make(app('files')->get($file_path), 200)
            ->header('Content-Type', app('files')->mimeType($file_path))
            ->header('Content-Disposition', 'inline; filename="' . $file->original_name . '"');
    }

    /**
     * FIXME automatic model binding does not work here, dunno why - alitak@20200525
     * @param int $file
     * @return \Illuminate\Http\Response
     * @throws FileNotFoundException
     */
    public function retrieveBase64($file)
    {
        $file_path = storage_path('app/' . self::STORAGE_DIR . File::query()->findOrFail($file)->file_path);
        if (app('files')->missing($file_path)) {
            abort(404);
        }

        return Response::make('data:' . app('files')->mimeType($file_path) . ';base64,' . base64_encode(app('files')->get($file_path)), 200);
    }

    public function download(File $file)
    {
        // FIXME
    }

    /**
     * Stores file in storage and creates db entry
     * @param UploadedFile $file
     * @param Partner|int $partner
     * @param string|null $storage_dir
     * @return File|Builder|Model
     */
    public static function store(UploadedFile $file, $partner = null, string $storage_dir = null): File
    {
        $partner_id = $partner === null ? null : ($partner instanceof Partner ? $partner->id : $partner);
        $storage_dir = $storage_dir ?? $partner_id;
        $path = Str::replaceFirst(self::STORAGE_DIR, '', $file->store(self::STORAGE_DIR . $storage_dir));
        return File::query()->create([
            'partner_id' => $partner_id,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_path' => $path,
        ]);
    }

}
