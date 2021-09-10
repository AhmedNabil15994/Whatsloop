<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Jobs;
use Stancl\Tenancy\Listeners;
use Stancl\Tenancy\Middleware;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;

class TenancyServiceProvider extends ServiceProvider
{
    // By default, no namespace is used to support the callable array syntax.
    public static string $controllerNamespace = '';

    public function events()
    {
        return [
            // Tenant events
            Events\CreatingTenant::class => [],
            Events\TenantCreated::class => [
                JobPipeline::make([
                    Jobs\CreateDatabase::class,
                    Jobs\MigrateDatabase::class,
                    // Jobs\SeedDatabase::class,

                    // Your own jobs to prepare the tenant.
                    // Provision API keys, create S3 buckets, anything you want!

                ])->send(function (Events\TenantCreated $event) {
                    return $event->tenant;
                })->shouldBeQueued(false), // `false` by default, but you probably want to make this `true` for production.
            ],
            Events\SavingTenant::class => [],
            Events\TenantSaved::class => [],
            Events\UpdatingTenant::class => [],
            Events\TenantUpdated::class => [],
            Events\DeletingTenant::class => [],
            Events\TenantDeleted::class => [
                JobPipeline::make([
                    Jobs\DeleteDatabase::class,
                ])->send(function (Events\TenantDeleted $event) {
                    return $event->tenant;
                })->shouldBeQueued(false), // `false` by default, but you probably want to make this `true` for production.
            ],

            // Domain events
            Events\CreatingDomain::class => [],
            Events\DomainCreated::class => [],
            Events\SavingDomain::class => [],
            Events\DomainSaved::class => [],
            Events\UpdatingDomain::class => [],
            Events\DomainUpdated::class => [],
            Events\DeletingDomain::class => [],
            Events\DomainDeleted::class => [],

            // Database events
            Events\DatabaseCreated::class => [],
            Events\DatabaseMigrated::class => [],
            Events\DatabaseSeeded::class => [],
            Events\DatabaseRolledBack::class => [],
            Events\DatabaseDeleted::class => [],

            // Tenancy events
            Events\InitializingTenancy::class => [],
            Events\TenancyInitialized::class => [
                Listeners\BootstrapTenancy::class,
            ],

            Events\EndingTenancy::class => [],
            Events\TenancyEnded::class => [
                Listeners\RevertToCentralContext::class,
            ],

            Events\BootstrappingTenancy::class => [],
            Events\TenancyBootstrapped::class => [
               //config(['auth.providers.users.model' => User::class])
            ],
            Events\RevertingToCentralContext::class => [],
            Events\RevertedToCentralContext::class => [],

            // Resource syncing
            Events\SyncedResourceSaved::class => [
                Listeners\UpdateSyncedResource::class,
            ],

            // Fired only when a synced resource is changed in a different DB than the origin DB (to avoid infinite loops)
            Events\SyncedResourceChangedInForeignDatabase::class => [],
        ];
    }

    public function register()
    {
        //
    }

    public function boot()
    {
        $this->bootEvents();
        $this->mapRoutes();

        $this->makeTenancyMiddlewareHighestPriority();
    }

    protected function bootEvents()
    {
        foreach ($this->events() as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                if ($listener instanceof JobPipeline) {
                    $listener = $listener->toListener();
                }

                Event::listen($event, $listener);
            }
        }
    }

    protected function mapRoutes()
    {
        // if (file_exists(base_path('routes/tenant.php'))) {
        //     Route::namespace(static::$controllerNamespace)
        //         ->group(base_path('routes/tenant.php'));
        // }

        $this->mapGuestRoutes();

        $this->mapModuleRoutes();

        // Route::middleware([
        //     'web',
        //     InitializeTenancyBySubdomain::class,
        //     PreventAccessFromCentralDomains::class,
        // ])->namespace(static::$controllerNamespace)
        //     ->group(function () {
        //     require app_path('Modules/Tenancy/Home/routes.php');
        // });
    }

    protected function mapGuestRoutes()
    {
        @define('DATE_TIME', date("Y-m-d H:i:s"));
        Route::middleware([
            'tenancyGeneral',
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
            ScopeSessions::class,
        ])->namespace(static::$controllerNamespace)
            ->group(function () {
            require app_path('Modules/Tenancy/Auth/routes.php');
            require app_path('Modules/Tenancy/WhatsLoop/routes.php');
        });
    }

    protected function mapModuleRoutes()
    {
        @define('DATE_TIME', date("Y-m-d H:i:s"));
        Route::middleware([
            'tenancyWithAuth',
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
            ScopeSessions::class,
        ])->namespace(static::$controllerNamespace)->group(function (){
            require app_path('Modules/Tenancy/Dashboard/routes.php');
            require app_path('Modules/Tenancy/User/routes.php');
            require app_path('Modules/Tenancy/Group/routes.php');
            require app_path('Modules/Tenancy/Bot/routes.php');
            require app_path('Modules/Tenancy/Template/routes.php');
            require app_path('Modules/Tenancy/Reply/routes.php');
            require app_path('Modules/Tenancy/Category/routes.php');
            require app_path('Modules/Tenancy/GroupNumbers/routes.php');
            require app_path('Modules/Tenancy/Contact/routes.php');
            require app_path('Modules/Tenancy/APIMods/routes.php');
            require app_path('Modules/Tenancy/GroupMsgs/routes.php');
            require app_path('Modules/Tenancy/ExternalServices/routes.php');
            require app_path('Modules/Tenancy/Profile/routes.php');
            require app_path('Modules/Tenancy/LiveChat/routes.php');
            require app_path('Modules/Tenancy/Ticket/routes.php');
            require app_path('Modules/Tenancy/UserStorage/routes.php');
            require app_path('Modules/Tenancy/Invoice/routes.php');
            require app_path('Modules/Tenancy/WhatsappOrder/routes.php');
        });
    }

    protected function makeTenancyMiddlewareHighestPriority()
    {
        $tenancyMiddleware = [
            // Even higher priority than the initialization middleware
            Middleware\PreventAccessFromCentralDomains::class,

            Middleware\InitializeTenancyByDomain::class,
            Middleware\InitializeTenancyBySubdomain::class,
            Middleware\InitializeTenancyByDomainOrSubdomain::class,
            Middleware\InitializeTenancyByPath::class,
            Middleware\InitializeTenancyByRequestData::class,
        ];

        foreach (array_reverse($tenancyMiddleware) as $middleware) {
            $this->app[\Illuminate\Contracts\Http\Kernel::class]->prependToMiddlewarePriority($middleware);
        }
    }
}
