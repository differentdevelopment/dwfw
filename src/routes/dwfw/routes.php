<?php

//use Different\Dwfw\app\Http\Controllers\LogsCrudController;
use Different\Dwfw\app\Http\Controllers\AccountsCrudController;
use Different\Dwfw\app\Http\Controllers\LogsCrudController;
use Different\Dwfw\app\Http\Controllers\PartnersCrudController;
use Different\Dwfw\app\Http\Controllers\SpammersCrudController;
use Different\Dwfw\app\Http\Controllers\UsersCrudController;
use Different\Dwfw\app\Http\Controllers\AuthController;
use Different\Dwfw\app\Http\Middleware\DisableDebugbarMiddleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

//use Illuminate\Support\Facades\Auth; //TODO EZ A KÉT SOR ERRORT OKOZOTT MINDEN RENDSZERÜNKBEN
//Auth::routes(['verify' => true]);

Route::group([
    'namespace' => 'Different\Dwfw\app\Http\Controllers',
    'middleware' => ['web'],
], function () {
    Route::group([
        'middleware' => [DisableDebugbarMiddleware::class],
    ], function () {
        Route::get('/{disk}/file/{file}', 'Files@retrieve')->name('file')->middleware('can:viewFile');
        Route::get('/{disk}/file_b64/{file}', 'Files@retrieveBase64')->name('file-b64')->middleware('can:viewFile');
    });

    Route::post('set_timezone', 'TimeZones@set')->name('set-timezone');
});

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin'), 'timezone',],
    'as' => 'admin',
], function () { // custom admin routes
    Route::get('logs/ajax-user-options', [LogsCrudController::class, 'userOptions'])->name('.ajax-user-options');
    Route::crud('/logs', LogsCrudController::class);
    Route::crud('/spammers', SpammersCrudController::class);
    Route::crud('/accounts', class_exists('App\Http\Controllers\Admin\AccountsCrudController')
        ? 'App\Http\Controllers\Admin\AccountsCrudController'
        : AccountsCrudController::class
    );

    // USERS
    Route::crud('/users', class_exists('App\Http\Controllers\Admin\UsersCrudController')
        ? \App\Http\Controllers\Admin\UsersCrudController::class
        : UsersCrudController::class
    );
    Route::get('/users/{user}/verify', [UsersCrudController::class, 'verifyUser'])->name('.verify');
    Route::get('/user', [UsersCrudController::class, 'abortUserGrid']);
    Route::post('/change-account', [AuthController::class, 'changeAccount'])->name('.change_account');
    
    // PARTNERS
    Route::group([
        'prefix' => 'partners',
        'as' => '.partners',
    ], function () {
        Route::get('ajax_partner_list', [PartnersCrudController::class, 'ajaxList'])->name('.ajax-partner-list');
        Route::crud('', PartnersCrudController::class);
    });

});

Artisan::command('logs:clear', function () {
    exec('echo "" > ' . storage_path('logs/laravel.log'));
    $this->comment('Logs have been cleared!');
})->describe('Clear log files');
