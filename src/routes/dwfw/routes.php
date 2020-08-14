<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace' => 'Different\Dwfw\app\Http\Controllers',
        'middleware' => ['web'],
    ],
    function () {
        Route::get('/{disk}/file/{file}', 'Files@retrieve')->name('file')->middleware('can:viewFile');
        Route::get('/{disk}/file_b64/{file}', 'Files@retrieveBase64')->name('file-b64')->middleware('can:viewFile');

        Route::post('set_timezone', 'TimeZones@set')->name('set-timezone');
    }
);

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin'), 'timezone',],
    'namespace' => 'Different\Dwfw\app\Http\Controllers',
    'as' => 'admin.',
], function () { // custom admin routes
    Route::crud('/logs', LogsCrudController::class);

    // USERS
    Route::crud('/users', UsersCrudController::class);
    Route::get('/users/{user}/verify', 'UsersCrudController@verifyUser')->name('verify');
    Route::get('/user', 'UsersCrudController@abortUserGrid');

    // PARTNERS
    Route::group([
        'prefix' => 'partners',
        'as' => 'partners',
    ], function () {
        Route::get('ajax_partner_list', 'PartnersCrudController@ajaxList')->name('.ajax-partner-list');
        Route::crud('', PartnersCrudController::class);
    });

});

Artisan::command('logs:clear', function() {
    exec('echo "" > ' . storage_path('logs/laravel.log'));
    $this->comment('Logs have been cleared!');
})->describe('Clear log files');
