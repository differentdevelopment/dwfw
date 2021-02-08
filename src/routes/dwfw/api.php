<?php

//use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Middleware\CorsMiddleware;
use Different\Dwfw\app\Http\Middleware\DisableDebugbarMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => '/v1/',
    'namespace' => 'Api\V1',
    'middleware' => [
        DisableDebugbarMiddleware::class,
        CorsMiddleware::class,
    ],
], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register']);
    Route::post('lost-password', [AuthController::class, 'lostPassword']);
    Route::post('password-recovery', [AuthController::class, 'passwordRecovery']);
    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::post('register-confirm', [AuthController::class, 'registerConfirm']);
        Route::get('new-pin', [AuthController::class, 'newPin']);
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

