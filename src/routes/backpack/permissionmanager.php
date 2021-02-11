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

use Different\Dwfw\app\Http\Controllers\PermissionCrudController;
use Different\Dwfw\app\Http\Controllers\RolesCrudController;
use Spatie\Permission\Middlewares\PermissionMiddleware;

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', backpack_middleware()],
], function () {

    Route::group(['middleware' => PermissionMiddleware::class . ':manage permissions',], function () {
        Route::crud('permission', PermissionCrudController::class);
    });
    Route::group(['middleware' => PermissionMiddleware::class . ':manage roles'], function () {
        Route::crud('role', RolesCrudController::class);
    });

    Route::crud('users', \Different\Dwfw\app\Http\Controllers\UsersCrudController::class);
});
