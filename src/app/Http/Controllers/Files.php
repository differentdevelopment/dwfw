<?php

namespace Different\Dwfw\app\Http\Controllers;

use Different\Dwfw\app\Models\File;
use Different\Dwfw\app\Models\Partner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Files extends Controller
{

    public const STORAGE_DIR = 'files/';
    public const DEFAULT_DISK = 'files';

    /**
     * @param File $file
     * @param string $disk
     * @return StreamedResponse @return StreamedResponse
     */
    public function retrieve(string $disk, File $file)
    {
        $file_path = Storage::disk($disk)->path($file->file_path);

        if (app('files')->missing($file_path)) {
            abort(404);
        }

        // https://stackoverflow.com/a/29997555
        $size = app('files')->size($file_path);
        $stream = fopen($file_path, "r");

        $start = 0;
        $length = $size;
        $status = 200;

        $headers = [
            'Content-Disposition' => 'inline; filename="' . $file->original_name . '"',
            'Content-Type' => app('files')->mimeType($file_path),
            'Accept-Ranges' => 'bytes'
        ];

        if (false !== $range = Request::server('HTTP_RANGE', false)) {
            [$param, $range] = explode('=', $range);
            if (strtolower(trim($param)) !== 'bytes') {
                header('HTTP/1.1 400 Invalid Request');
                exit;
            }
            [$from, $to] = explode('-', $range);
            if ($from === '') {
                $end = $size - 1;
                $start = $end - intval($from);
            } elseif ($to === '') {
                $start = intval($from);
                $end = $size - 1;
            } else {
                $start = intval($from);
                $end = intval($to);
            }
            $length = $end - $start + 1;
            $status = 206;
            $headers['Content-Range'] = sprintf('bytes %d-%d/%d', $start, $end, $size);
        }

        return Response::stream(function () use ($stream, $start, $length) {
            fseek($stream, $start, SEEK_SET);
            echo fread($stream, $length);
            fclose($stream);
        }, $status, $headers);
    }

    /**
     * @param string $disk
     * @param File $file
     * @return \Illuminate\Http\Response
     */
    public function retrieveBase64(string $disk, File $file)
    {
        $file_path = Storage::disk($disk)->path($file->file_path);
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
     * @param null $partner
     * @param string|null $storage_dir
     * @param string|null $disk
     * @return File|Builder|Model
     */
    public static function store(UploadedFile $file, $partner = null, string $storage_dir = null, ?string $disk = self::DEFAULT_DISK): File
    {
        return Files::insertUploadedFileIntoDb($file, $partner, $storage_dir, $file->getClientOriginalName(), $file->getMimeType(), $disk);
    }

    /**
     * Stores base64 image in storage as file and creates db entry
     * @param UploadedFile $file
     * @param Partner|int $partner
     * @param string|null $storage_dir
     * @return File|Builder|Model
     */
    public static function storeBase64(string $base64, $partner = null, string $storage_dir = null, ?string $disk = self::DEFAULT_DISK): File
    {
        $image_parts = explode(";base64,", $base64);
        $image_type_aux = explode("data:", $image_parts[0]);
        $image_type_file = explode("/", $image_type_aux[1]);
        $safe_name = Str::uuid()->toString() . '.' . $image_type_file[1];

        $file_data = base64_decode($image_parts[1]);
        $tmp_file_path = sys_get_temp_dir() . '/' . Str::uuid()->toString();
        file_put_contents($tmp_file_path, $file_data);
        $tmp_file = new \Illuminate\Http\File($tmp_file_path);
        $file = new UploadedFile(
            $tmp_file->getPathname(),
            $tmp_file->getFilename(),
            $tmp_file->getMimeType(),
            0,
            true
        );

        return Files::insertUploadedFileIntoDb($file, $partner, $storage_dir, $safe_name, $image_type_aux[1], $disk);
    }

    /**
     * Stores the UploadedFile and creates the db entry
     * @param UploadedFile $file
     * @param null $partner
     * @param string|null $storage_dir
     * @param string|null $original_name
     * @param string|null $mime_type
     * @param string|null $disk
     * @return File|Builder|Model
     */
    private static function insertUploadedFileIntoDb(
        UploadedFile $file,
        $partner = null,
        string $storage_dir = null,
        string $original_name = null,
        string $mime_type = null,
        ?string $disk = null
    ): File
    {
        $partner_id = $partner === null ? null : ($partner instanceof \App\Models\Partner ? $partner->id : $partner);
        $storage_dir = $storage_dir ?? $partner_id;
        Storage::disk($disk)->put($storage_dir, $file);
        return File::query()->create([
            'partner_id' => $partner_id,
            'original_name' => $original_name,
            'mime_type' => $mime_type,
            'file_path' => $file->hashName(),
        ]);
    }

}
