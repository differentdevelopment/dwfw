<?php

namespace Different\Dwfw;

use App\Http\Controllers\Controller;
use App\Models\User;
use Backpack\Settings\app\Models\Setting;
use Different\Dwfw\app\Console\Commands\Install;
use Different\Dwfw\app\Console\Commands\InstallPassport;
use Different\Dwfw\app\Console\Commands\Upgrade;
use Different\Dwfw\app\Console\LogDeleteSchedule;
use Different\Dwfw\app\Http\Middleware\ConvertIdToTimeZone;
use Different\Dwfw\app\Listeners\SetUserSession;
use Different\Dwfw\app\Models\Partner;
use Different\Dwfw\app\Observers\PartnerObserver;
use Different\Dwfw\app\Observers\SettingObserver;
use Different\Dwfw\app\Observers\UserObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class DwfwServiceProvider extends ServiceProvider
{
    private $commands = [
        Install::class,
        Upgrade::class,
        InstallPassport::class,
        
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
        $this->app->singleton(SetUserSession::class, function () {
            return new SetUserSession();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Model::preventLazyLoading(!$this->app->isProduction());        
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super admin') ? true : null;
        });

        if (method_exists(Controller::class, 'allowFileView')) {
            Gate::define('viewFile', 'App\Http\Controllers\Controller@allowFileView');
        } else {
            Gate::define('viewFile', function ($user = null) {
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

        $this->loadRoutesFrom(__DIR__ . '/routes/backpack/settings.php');

        $this->loadRoutesFrom(__DIR__ . '/routes/backpack/permissionmanager.php');

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'dwfw');

        $this->loadTranslationsFrom(__DIR__ . '/resources/lang/dwfw', 'dwfw');

        $this->commands($this->commands);

        $this->publishFiles();

        // removed unused files from base Laravel install
        $this->cleanup();

        $this->setBladeDirectives();
    }

    private function setBladeDirectives(): void
    {
        Blade::directive('active', fn($expression) => "<?php echo request()->is('admin/' . $expression . '*') ? 'active' : '' ?>");

        Blade::if('dwfwcan', function (string|array $permission) {
            $user = backpack_user();
            if ($user->hasRole('super admin')) return true;

            return $user->hasAnyPermission($permission);
        });

        Blade::if('count', fn(\Countable $dataset) => count($dataset) > 0);
    }

    public function registerMiddlewareGroup(Router $router)
    {
        foreach ($this->middlewares as $middleware) {
            $router->pushMiddlewareToGroup('timezone', $middleware);
        }
    }

    private function publishFiles()
    {
        /*
        |--------------------------------------------------------------------------
        | BASE GROUP - PUBLISHED BY DWFW:INSTALL
        |--------------------------------------------------------------------------
        */

        // Models
        $this->publishes([__DIR__ . '/app/Models/User.php' => app_path('Models/User.php')], ['base', 'models.user']);

        // Database seeder
        $this->publishes([__DIR__ . '/database/seeds/DatabaseSeeder.php' => database_path('seeds/DatabaseSeeder.php')], ['base', 'seeds.database']);

        // Backpack related configs
        $this->publishes([__DIR__ . '/config/backpack/base.php' => config_path('backpack/base.php')], ['base', 'config.backpack.base']);
        $this->publishes([__DIR__ . '/config/backpack/permissionmanager.php' => config_path('backpack/permissionmanager.php')], ['base', 'config.backpack.permissionmanager']);

        // backpack default view files, snippets
        $this->publishes([__DIR__ . '/resources/views/sidebar_content.blade.php' => resource_path('views/vendor/backpack/base/inc/sidebar_content.blade.php')], ['base', 'views.backpack.base.sidebar']);
        $this->publishes([__DIR__ . '/resources/views/upload.blade.php' => resource_path('views/vendor/backpack/crud/fields/upload.blade.php')], ['base', 'views.backpack.crud.fields.upload']);
        $this->publishes([__DIR__ . '/resources/views/checklist_dependency.blade.php' => resource_path('views/vendor/backpack/crud/fields/checklist_dependency.blade.php')], ['base', 'views.backpack.crud.fields.checklist_dependency']);
        $this->publishes([__DIR__. '/resources/views/permission_dependency.blade.php' => resource_path('views/vendor/backpack/crud/fields/permission_dependency.blade.php')], ['base', 'views.backpack.crud.fields.permission_dependency']);
        $this->publishes([__DIR__. '/resources/views/selectAll.blade.php' => resource_path('views/vendor/backpack/crud/fields/selectAll.blade.php')], ['base', 'views.backpack.crud.fields.selectAll']);
        // core configs
        $this->publishes([__DIR__ . '/config/auth.php' => config_path('auth.php')], ['base', 'config.auth']);
        $this->publishes([__DIR__ . '/config/cache.php' => config_path('cache.php')], ['base', 'config.cache']);

        // languages
        $this->publishes([__DIR__ . '/resources/lang/hu/validation.php' => resource_path('lang/hu/validation.php')], ['base', 'core.langs']);
        $this->publishes([__DIR__ . '/resources/lang/hu/passwords.php' => resource_path('lang/hu/passwords.php')], ['base', 'core.langs']);
        $this->publishes([__DIR__ . '/resources/lang/hu/pagination.php' => resource_path('lang/hu/pagination.php')], ['base', 'core.langs']);
        $this->publishes([__DIR__ . '/resources/lang/hu/auth.php' => resource_path('lang/hu/auth.php')], ['base', 'core.langs']);
        $this->publishes([__DIR__ . '/resources/lang/hu.json' => resource_path('lang/hu.json')], ['base', 'core.langs']);

        //backpack languages
        $this->publishes([__DIR__ . '/resources/lang/backpack/hu/base.php' => resource_path('lang/vendor/backpack/hu/base.php')], ['base', 'backpack.langs']);
        $this->publishes([__DIR__ . '/resources/lang/backpack/hu/crud.php' => resource_path('lang/vendor/backpack/hu/crud.php')], ['base', 'backpack.langs']);
        $this->publishes([__DIR__ . '/resources/lang/backpack/hu/permissionmanager.php' => resource_path('lang/vendor/backpack/hu/permissionmanager.php')], ['base', 'backpack.langs', 'permission.lang']);
        $this->publishes([__DIR__ . '/resources/lang/backpack/hu/settings.php' => resource_path('lang/vendor/backpack/hu/settings.php')], ['base', 'backpack.langs']);

        //config
        $this->publishes([__DIR__ . '/config/checkIp.php' => config_path('checkIp.php')], ['base', 'config.checkIp']);
        $this->publishes([__DIR__ . '/config/permission.php' => config_path('permission.php')], ['base', 'config.permission']);

        //Backpack login view
        $this->publishes([__DIR__ . '/resources/views/vendor/backpack/base/auth/login.blade.php' => resource_path() . '/views/vendor/backpack/base/auth/login.blade.php'], ['base', 'backpack.login']);

        //Select2 Ajax multiple filter for Backpack
        $this->publishes([__DIR__ . '/resources/views/vendor/backpack/crud/filters/select2_ajax_multiple.blade.php' => resource_path() . '/views/vendor/backpack/crud/filters/select2_ajax_multiple.blade.php'], ['base', 'backpack.filters']);

        //Spatie Honey
        $this->publishes([__DIR__ . '/app/SpamResponder/SpamRespond.php' => app_path() . '/SpamResponder/SpamRespond.php'], ['base', 'spatie-honey.spam-respond']);

        // Fixed custom routes
        $this->publishes([__DIR__ . '/routes/backpack/custom.php' => base_path() . '/routes/custom.php'], ['base', 'backpack.custom']);

        /*
        |--------------------------------------------------------------------------
        | ACCOUNT HANDLING
        |
        | Publish command: php artisan vendor:publish --provider=Different\Dwfw\DwfwServiceProvider --tag=accounts --force
        |--------------------------------------------------------------------------
        */

        //backpack views & components
        $this->publishes([__DIR__ . '/resources/views/components/account-selector.blade.php' => resource_path() . '/views/components/account-selector.blade.php'], ['accounts', 'backpack.account.views']);
        $this->publishes([__DIR__ . '/resources/views/topbar_right_content.blade.php' => resource_path() . '/views/vendor/backpack/base/inc/topbar_right_content.blade.php'], ['accounts', 'backpack.account.views']);
        $this->publishes([__DIR__ . '/app/View/Components/AccountSelector.php' => app_path() . '/View/Components/AccountSelector.php'], ['accounts', 'backpack.account.components']);
        $this->publishes([__DIR__ . '/public/scripts/all.js' => public_path() . '/scripts/all.js'], ['accounts', 'backpack.account.scripts']);

        /*
        |--------------------------------------------------------------------------
        | PASSPORT GROUP PUBLISHED BY DWFW:INSTALL-PASSPORT
        |--------------------------------------------------------------------------
        */

        //Auth Controller
        $this->publishes([__DIR__ . '/app/Http/Controllers/Api/V1/AuthController.php' => app_path() . '/Http/Controllers/Api/V1/AuthController.php'], ['passport', 'auth.controller']);

        //Requests
        $this->publishes([__DIR__ . '/app/Http/Requests/Api/BaseApiFormRequest.php' => app_path() . '/Http/Requests/Api/BaseApiFormRequest.php'], ['passport', 'requests.base-api']);
        $this->publishes([__DIR__ . '/app/Http/Requests/Api/V1/AuthLoginRequest.php' => app_path() . '/Http/Requests/Api/V1/AuthLoginRequest.php'], ['passport', 'requests.login']);
        $this->publishes([__DIR__ . '/app/Http/Requests/Api/V1/AuthLostPasswordRequest.php' => app_path() . '/Http/Requests/Api/V1/AuthLostPasswordRequest.php'], ['passport', 'requests.lost-password']);
        $this->publishes([__DIR__ . '/app/Http/Requests/Api/V1/AuthPasswordRecoveryRequest.php' => app_path() . '/Http/Requests/Api/V1/AuthPasswordRecoveryRequest.php'], ['passport', 'requests.password-recovery']);
        $this->publishes([__DIR__ . '/app/Http/Requests/Api/V1/AuthRegisterRequest.php' => app_path() . '/Http/Requests/Api/V1/AuthRegisterRequest.php'], ['passport', 'requests.register']);
        $this->publishes([__DIR__ . '/app/Http/Requests/Api/V1/AuthRegisterConfirmRequest.php' => app_path() . '/Http/Requests/Api/V1/AuthRegisterConfirmRequest.php'], ['passport', 'requests.register-confirm']);

        // Models
        $this->publishes([__DIR__ . '/app/Models/PasswordReset.php' => app_path() . '/Models/PasswordReset.php'], ['passport', 'models.password-reset']);

        //Routes
        $this->publishes([__DIR__ . '/routes/dwfw/api.php' => base_path() . '/routes/api.php'], ['passport', 'routes.api']);

        //Middlewares
        $this->publishes([__DIR__ . '/app/Http/Middleware/CorsMiddleware.php' => app_path() . '/Http/Middleware/CorsMiddleware.php'], ['passport', 'middlewares.cors']);

        //Notifications
        $this->publishes([__DIR__ . '/app/Notifications/CustomPasswordResetNotification.php' => app_path() . '/Notifications/CustomPasswordResetNotification.php'], ['passport', 'middlewares.cors']);
        $this->publishes([__DIR__ . '/app/Notifications/CustomRegistrationConfirmNotification.php' => app_path() . '/Notifications/CustomRegistrationConfirmNotification.php'], ['passport', 'middlewares.cors']);

        //Postman Collection
        $this->publishes([__DIR__ . '/../Dwfw.postman_collection.json' => base_path() . env('APP_NAME') . '.postman_collection.json'], ['passport', 'auth.postman']);
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
