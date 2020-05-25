<?php

namespace Different\Dwfw;

use Different\Dwfw\app\Console\Commands\DwfwSeederCommand;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class DwfwServiceProvider extends ServiceProvider
{
    private $commands = [
        DwfwSeederCommand::class,
    ];
    private $middlewares = [
        \Different\Dwfw\app\Http\Middleware\ConvertIdToTimeZone::class,
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
        $this->registerMiddlewareGroup($this->app->router);

        $this->loadMigrationsFrom(realpath(__DIR__ . '/database/migrations/'));

        $this->loadRoutesFrom(__DIR__ . '/routes/dwfw/routes.php');

        // register the artisan commands
        $this->commands($this->commands);

        // publish necessary files
        $this->publishFiles();

        // removed unused files from base Laravel install
        $this->cleanup();
    }

    public function registerMiddlewareGroup(Router $router)
    {
        foreach ($this->middlewares as $middleware) {
            $router->pushMiddlewareToGroup('timezone', $middleware);
        }
    }

    private function publishFiles()
    {
        $this->publishes([__DIR__ . '/app/Models/User.php' => app_path('Models/User.php')], 'models.user');
        $this->publishes([__DIR__ . '/app/Models/Partner.php' => app_path('Models/Partner.php')], 'models.partner');
        $this->publishes([__DIR__ . '/database/seeds/' => database_path('seeds')], 'seeds');
        $this->publishes([__DIR__ . '/config/backpack/base.php' => config_path('backpack/base.php')], 'config.backpack.base');
    }

    private function cleanup()
    {
        if (File::exists(app_path('User.php'))) {
            File::delete(app_path('User.php'));
        }
    }
}
