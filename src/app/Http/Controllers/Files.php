<?php

namespace Different\Dwfw\app\Http\Controllers;

use Different\Dwfw\app\Models\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File as CoreFile;
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
        $file_path = Storage::path($file->file_path);

        if (!Storage::exists($file_path)) {
            abort(404);
        }

        return Storage::response($file_path);
    }

    public function retrieveByteStream(string $disk, File $file)
    {
        $file_path = Storage::path($file->file_path);
        
        if (!Storage::exists($file_path)) {
            abort(404);
        }

        // https://stackoverflow.com/a/29997555
        $size = Storage::size($file_path);
        $stream = fopen($file_path, "r");

        $start = 0;
        $length = $size;
        $status = 200;

        $headers = [
            'Content-Disposition' => 'inline; filename="' . $file->original_name . '"',
            'Content-Type' => Storage::mimeType($file_path),
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
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function retrieveBase64(string $disk, File $file)
    {
        $file_path = Storage::path($file->file_path);

        if (!Storage::exists($file_path)) {
            abort(404);
        }

        return Response::make('data:' . Storage::mimeType($file_path) . ';base64,' . base64_encode(Storage::get($file_path)), 200);
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
     * @param string $base64
     * @param null $partner
     * @param string|null $storage_dir
     * @param string|null $disk
     * @param string|null $original_name
     * @param int|null $file_id
     * @return File
     */
    public static function storeBase64(string $base64, $partner = null, string $storage_dir = null, ?string $disk = self::DEFAULT_DISK, ?string $original_name = null, ?int $file_id = null): File
    {
        $image_parts = explode(";base64,", $base64);
        $image_type_aux = explode("data:", $image_parts[0]);
        $image_type_file = explode("/", $image_type_aux[1]);
        $safe_name = $original_name ?? Str::uuid()->toString() . '.' . $image_type_file[1];

        /*$tmp_file_path = sys_get_temp_dir() . '/' . ($file_id ? Str::beforeLast(Str::replace($storage_dir . '/', '', File::query()->find($file_id)->file_path), '.') : Str::uuid()->toString());*/
        $tmp_file_path = sys_get_temp_dir() . '/' . Str::uuid()->toString();
        $file_data = base64_decode($image_parts[1]);
        file_put_contents($tmp_file_path, $file_data);
        $tmp_file = new \Illuminate\Http\File($tmp_file_path);
        $uploaded_file = new UploadedFile(
            $tmp_file->getPathname(),
            $tmp_file->getFilename(),
            $tmp_file->getMimeType(),
            0,
            true
        );

        return Files::insertUploadedFileIntoDb($uploaded_file, $partner, $storage_dir, $safe_name, $image_type_aux[1], $disk, $file_id);
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
        UploadedFile $uploaded_file,
        $partner = null,
        string $storage_dir = null,
        string $original_name = null,
        string $mime_type = null,
        ?string $disk = null,
        ?int $file_id = null,
    ): File
    {
        $partner_id = $partner === null ? null : ($partner instanceof \App\Models\Partner ? $partner->id : $partner);
        $storage_dir = $storage_dir ?? $partner_id;

        $file = File::query()->findOrNew($file_id);
        Storage::delete($file->file_path);

        if ($storage_dir !== null) {
            $storage_dir .= '/';
        }

        $file->partner_id = $partner_id;
        $file->original_name = $original_name;
        $file->mime_type = $mime_type;
        $file->file_path = $storage_dir . $uploaded_file->hashName();
        $file->access_hash = Str::random(40);
        $file->save();

        if ($storage_dir === null) {
            $storage_dir = "";
        }

        $storage = Storage::putFile($storage_dir, $uploaded_file, 'public');
        return $file;
    }

    public static function delete(File $file, string $storage_dir = null, ?string $disk = self::DEFAULT_DISK)
    {
        $storage_dir = $storage_dir ?? $file->partner_id;
        if ($storage_dir !== null) {
            $storage_dir .= '/';
        }

        if (!Storage::delete($storage_dir . $file->file_path)) {
            // a törlés nem sikerült.. 404 jó ilyenkor? FIXME hogy milyen üzenetet dobjon ilyenkor! Kell-e egyáltalán, vagy szedje ki a db-ből és megvagyunk
            // abort(404);
        }
        $file->delete();
    }
}
