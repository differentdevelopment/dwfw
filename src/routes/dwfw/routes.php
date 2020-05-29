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

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin'), 'timezone',],
    'namespace' => 'Different\Dwfw\app\Http\Controllers',
    'as' => 'admin.',
], function () { // custom admin routes
    Route::crud('/users', UsersCrudController::class);
    Route::get('/users/{user}/verify', 'UsersCrudController@verifyUser')->name('verify');
    Route::get('/user', function () {
        abort(404);
    });

    Route::crud('/partners', PartnerCrudController::class);

});
