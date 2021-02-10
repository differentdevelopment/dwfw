<?php

/*
|--------------------------------------------------------------------------
| Backpack\PermissionManager Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are
| handled by the Backpack\PermissionManager package.
|
*/

use Spatie\Permission\Middlewares\PermissionMiddleware;

Route::group([
    'namespace' => 'Backpack\PermissionManager\app\Http\Controllers',
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', backpack_middleware()],
], function () {

    Route::group(['middleware' => PermissionMiddleware::class . ':manage permissions',], function () {
        Route::crud('permission', 'PermissionCrudController');
    });
    Route::group(['middleware' => PermissionMiddleware::class . ':manage roles'], function () {
        Route::crud('role', 'RoleCrudController');
    });
    Route::crud('user', 'UserCrudController');
});
