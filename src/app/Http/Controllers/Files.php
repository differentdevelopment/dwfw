<?php

namespace Different\Dwfw\app\Http\Controllers;

use App\Models\Partner;
use Different\Dwfw\app\Models\File;
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
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
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
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
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
     * @return File|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public static function store(UploadedFile $file, $partner = null): File
    {
        $partner_id = $partner === null ? null : ($partner instanceof Partner ? $partner->id : $partner);
        $path = Str::replaceFirst(self::STORAGE_DIR, '', $file->store(self::STORAGE_DIR . $partner_id));
        return File::query()->create([
            'partner_id' => $partner_id,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_path' => $path,
        ]);
    }

}
