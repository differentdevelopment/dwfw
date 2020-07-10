<?php

use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('logs:clear', function() {
    exec('echo "" > ' . storage_path('logs/laravel.log'));
    $this->comment('Logs have been cleared!');
})->describe('Clear log files');
