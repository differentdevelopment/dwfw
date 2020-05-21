<?php

namespace Different\Dwfw;

use Different\Dwfw\app\Console\Commands\DwfwSeederCommand;
use Illuminate\Support\ServiceProvider;

class DwfwServiceProvider extends ServiceProvider
{
    private $commands = [
        DwfwSeederCommand::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
//        $this->app->register(DwfwServiceProvider::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(realpath(__DIR__ . '/database/migrations/'));

        // register the artisan commands
        $this->commands($this->commands);

        $this->publishFiles();
    }

    private function publishFiles()
    {
        $this->publishes([__DIR__ . '/app/Models/User.php' => app_path('Models/User.php')], 'models.user');
        $this->publishes([__DIR__ . '/database/seeds/' => database_path('seeds')], 'seeds');
        $this->publishes([__DIR__ . '/config/backpack/base.php' => config_path('backpack/base.php')], 'config.backpack.base');
    }
}
