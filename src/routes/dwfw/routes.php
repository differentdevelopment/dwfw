<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace' => 'Different\Dwfw\app\Http\Controllers',
    ],
    function () {
        Route::get('/file/{file:id}', 'Files@retrieve')->name('file');
        Route::get('/file_b64/{file}', 'Files@retrieveBase64')->name('file-b64');

        Route::post('set_timezone', 'TimeZones@set')->name('set-timezone')->middleware(['web']);
    }
);

