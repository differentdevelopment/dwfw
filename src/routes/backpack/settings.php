<?php

/*
|--------------------------------------------------------------------------
| Backpack\Settings Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are
| handled by the Backpack\Settings package.
|
*/

Route::group([
    'namespace'  => 'Different\Dwfw\app\Http\Controllers',
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', backpack_middleware(), \Spatie\Permission\Middlewares\PermissionMiddleware::class . ':manage settings'],
], function () {
    Route::crud('setting', 'SettingController');
});
