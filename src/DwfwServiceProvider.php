<?php

namespace Different\Dwfw;

use App\Http\Controllers\Controller;
use App\Models\User;
use Backpack\Settings\app\Models\Setting;
use Different\Dwfw\app\Console\Commands\Install;
use Different\Dwfw\app\Console\Commands\Upgrade;
use Different\Dwfw\app\Http\Middleware\ConvertIdToTimeZone;
use Different\Dwfw\app\Models\Partner;
use Different\Dwfw\app\Observers\PartnerObserver;
use Different\Dwfw\app\Observers\SettingObserver;
use Different\Dwfw\app\Observers\UserObserver;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class DwfwServiceProvider extends ServiceProvider
{
    private $commands = [
        Install::class,
        Upgrade::class,
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
        if(method_exists(Controller::class, 'allowFileView')) {
            Gate::define('viewFile', 'App\Http\Controllers\Controller@allowFileView');
        } else{
            Gate::define('viewFile', function($user = null){
               return true;
            });
        }

        app()->config["filesystems.disks.files"] = [ //Registering the disk
            'driver' => 'local',
            'root' => storage_path('app/files'),
        ];

        $this->registerObservers();

        $this->registerMiddlewareGroup($this->app->router);

        $this->loadMigrationsFrom(realpath(__DIR__ . '/database/migrations/'));

        $this->loadRoutesFrom(__DIR__ . '/routes/dwfw/routes.php');

        $this->loadRoutesFrom(__DIR__ . '/routes/backpack/base.php');

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'dwfw');

        $this->loadTranslationsFrom(__DIR__ . '/resources/lang/dwfw', 'dwfw');

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

        // Database seeder
        $this->publishes([__DIR__ . '/database/seeds/DatabaseSeeder.php' => database_path('seeds/DatabaseSeeder.php')], 'seeds.database');

        // Backpack related configs
        $this->publishes([__DIR__ . '/config/backpack/base.php' => config_path('backpack/base.php')], 'config.backpack.base');
        $this->publishes([__DIR__ . '/config/backpack/permissionmanager.php' => config_path('backpack/permissionmanager.php')], 'config.backpack.permissionmanager');

        // backpack default view files, snippets
        $this->publishes([__DIR__ . '/resources/views/sidebar_content.blade.php' => resource_path('views/vendor/backpack/base/inc/sidebar_content.blade.php')], 'views.backpack.base.sidebar');
        $this->publishes([__DIR__ . '/resources/views/upload.blade.php' => resource_path('views/vendor/backpack/crud/fields/upload.blade.php')], 'views.backpack.crud.fields.upload');

        // core configs
        $this->publishes([__DIR__ . '/config/auth.php' => config_path('auth.php')], 'config.auth');

        // languages
        $this->publishes([__DIR__ . '/resources/lang/hu/validation.php' => resource_path('lang/hu/validation.php')], 'core.langs');
        $this->publishes([__DIR__ . '/resources/lang/hu/passwords.php' => resource_path('lang/hu/passwords.php')], 'core.langs');
        $this->publishes([__DIR__ . '/resources/lang/hu/pagination.php' => resource_path('lang/hu/pagination.php')], 'core.langs');
        $this->publishes([__DIR__ . '/resources/lang/hu/auth.php' => resource_path('lang/hu/auth.php')], 'core.langs');
        $this->publishes([__DIR__ . '/resources/lang/hu.json' => resource_path('lang/hu.json')], 'core.langs');

        //backpack languages
        $this->publishes([__DIR__ . '/resources/lang/backpack/hu/base.php' => resource_path('lang/vendor/backpack/hu/base.php')], 'backpack.langs');
        $this->publishes([__DIR__ . '/resources/lang/backpack/hu/crud.php' => resource_path('lang/vendor/backpack/hu/crud.php')], 'backpack.langs');
        $this->publishes([__DIR__ . '/resources/lang/backpack/hu/permissionmanager.php' => resource_path('lang/vendor/backpack/hu/permissionmanager.php')], 'backpack.langs');
        $this->publishes([__DIR__ . '/resources/lang/backpack/hu/settings.php' => resource_path('lang/vendor/backpack/hu/settings.php')], 'backpack.langs');


        //utilites
        $this->publishes([__DIR__ . DIRECTORY_SEPARATOR .'../tests/utilities/functions.php' => base_path() . '/tests/utilities/functions.php'], 'tests.utilities');

        //Backpack login view
        $this->publishes([__DIR__ . '/resources/views/vendor/backpack/base/auth/login.blade.php' => resource_path() . '/views/vendor/backpack/base/auth/login.blade.php'], 'backpack.login');
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
