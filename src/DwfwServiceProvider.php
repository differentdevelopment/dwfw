<?php

namespace Different\Dwfw;

use App\Models\User;
use Backpack\Settings\app\Models\Setting;
use Different\Dwfw\app\Console\Commands\Install;
use Different\Dwfw\app\Http\Middleware\ConvertIdToTimeZone;
use Different\Dwfw\app\Models\Partner;
use Different\Dwfw\app\Observers\PartnerObserver;
use Different\Dwfw\app\Observers\SettingObserver;
use Different\Dwfw\app\Observers\UserObserver;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class DwfwServiceProvider extends ServiceProvider
{
    private $commands = [
        Install::class,
    ];
    private $middlewares = [
        ConvertIdToTimeZone::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerObservers();

        $this->registerMiddlewareGroup($this->app->router);

        $this->loadMigrationsFrom(realpath(__DIR__ . '/database/migrations/'));

        $this->loadRoutesFrom(__DIR__ . '/routes/dwfw/routes.php');

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'dwfw');

        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'dwfw');

        $this->commands($this->commands);

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
        // Models
        $this->publishes([__DIR__ . '/app/Models/User.php' => app_path('Models/User.php')], 'models.user');

        // Factories
        $this->publishes([__DIR__ . '/database/factories/UserFactory.php' => database_path('factories/UserFactory.php')], 'factories.user');

        // Database seeder
        $this->publishes([__DIR__ . '/database/seeds/DatabaseSeeder.php' => database_path('seeds/DatabaseSeeder.php')], 'seeds.database');

        // Backpack related configs
        $this->publishes([__DIR__ . '/config/backpack/base.php' => config_path('backpack/base.php')], 'config.backpack.base');
        $this->publishes([__DIR__ . '/config/backpack/base.php' => config_path('backpack/base.php')], 'config.backpack.base');
        $this->publishes([__DIR__ . '/config/backpack/permissionmanager.php' => config_path('backpack/permissionmanager.php')], 'config.backpack.permissionmanager');

        // backpack default view files, snippets
        $this->publishes([__DIR__ . '/resources/views/sidebar_content.blade.php' => resource_path('views/vendor/backpack/base/inc/sidebar_content.blade.php')], 'views.backpack.base.sidebar');
        $this->publishes([__DIR__ . '/resources/views/upload.blade.php' => resource_path('views/vendor/backpack/crud/fields/upload.blade.php')], 'views.backpack.crud.fields.upload');

        // core configs
        $this->publishes([__DIR__ . '/config/auth.php' => config_path('auth.php')], 'config.auth');
    }

    private function cleanup()
    {
        if (File::exists(app_path('User.php'))) {
            File::delete(app_path('User.php'));
        }
        if (File::exists(app_path('Http/Middleware/CheckIfAdmin.php'))) {
            File::delete(app_path('Http/Middleware/CheckIfAdmin.php'));
        }
    }

    public function registerObservers()
    {
        if (File::exists(app_path('Models/User.php'))) {
            User::observe(UserObserver::class);
        }
        Setting::observe(SettingObserver::class);
        Partner::observe(PartnerObserver::class);
    }
}
