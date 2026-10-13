<?php

namespace Synchub\LaravelSynchub\Infrastructure\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Synchub\LaravelSynchub\Console\Commands\MakeSyncContextCommand;
use Synchub\LaravelSynchub\Console\Commands\MakeSynchubCommand;
use SyncHub\LaravelSyncHub\Console\Commands\PublishRoutesCommand;

class LaravelSynchubServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(
            CoreServiceProvider::class
        );

        $this->app->register(
            PersistenceServiceProvider::class
        );

        $this->app->register(
            EventServiceProvider::class
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../../../config/synchub.php',
            'synchub'
        );

        if ($this->app->runningInConsole()) {

            $this->commands([
                MakeSyncContextCommand::class,
                MakeSynchubCommand::class,
                PublishRoutesCommand::class,
            ]);
        }
    }


    public function boot(): void
    {
        $this->registerRoutes();

        $this->loadViewsFrom(
            __DIR__ . '/../../../resources/views',
            'synchub'
        );

        $this->loadMigrationsFrom(
            __DIR__ . '/../../../database/migrations'
        );

        $this->publishes([
            __DIR__ . '/../../../config/synchub.php'
            => config_path('synchub.php'),
        ], 'laravel-synchub-config');

        $this->publishes([
            __DIR__ . '/../../../resources/views'
            => resource_path('views'),
        ], 'synchub-views');
    }

    /**
     * Register SyncHub routes.
     */
    protected function registerRoutes(): void
    {
        if (! config('synchub.routes.enabled', true)) {
            return;
        }

        $this->registerWebRoutes();
        $this->registerApiRoutes();
    }

    /**
     * Register web routes.
     */
    protected function registerWebRoutes(): void
    {
        if (! config('synchub.routes.web.enabled', true)) {
            return;
        }

        $path = $this->resolveRoutePath('web');

        if ($path === null) {
            return;
        }

        Route::middleware(
            config('synchub.routes.web.middleware', ['web'])
        )
            // ->prefix(config('synchub.routes.prefix', 'sync'))
            ->group($path);
    }

    /**
     * Register API routes.
     */
    protected function registerApiRoutes(): void
    {
        if (! config('synchub.routes.api.enabled', true)) {
            return;
        }

        $path = $this->resolveRoutePath('api');

        if ($path === null) {
            return;
        }

        Route::middleware(
            config('synchub.routes.api.middleware', ['api'])
        )
            // ->prefix(config('synchub.routes.api.prefix', 'synchub'))
            ->group($path);
    }

    /**
     * Resolve the route file according to the configured source.
     *
     * Supported sources:
     * - package: use the route file provided by SyncHub.
     * - application: use the route file published in the application.
     */
    protected function resolveRoutePath(string $type): ?string
    {
        $source = config(
            "synchub.routes.{$type}.source",
            'package'
        );

        if ($source === 'application') {
            $path = base_path(
                config(
                    "synchub.routes.{$type}.path",
                    "routes/synchub/{$type}.php"
                )
            );

            return is_file($path) ? $path : null;
        }

        if ($source !== 'package') {
            throw new \InvalidArgumentException(
                "Invalid SyncHub route source [{$source}] for [{$type}]."
            );
        }

        $path = __DIR__ . "/../../../routes/{$type}.php";

        return is_file($path) ? $path : null;
    }
}