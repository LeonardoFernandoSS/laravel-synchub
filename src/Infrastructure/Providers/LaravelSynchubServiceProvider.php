<?php

namespace Synchub\LaravelSynchub\Infrastructure\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Synchub\LaravelSynchub\Console\Commands\MakeSyncContextCommand;
use Synchub\LaravelSynchub\Console\Commands\MakeSynchubCommand;

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
            ]);
        }
    }


    public function boot(): void
    {
        if (config('synchub.routes.enabled')) {
            // Rotas da interface web: sessão, cookies e erros de validação.
            Route::middleware('web')->group(
                __DIR__ . '/../../../routes/web.php'
            );

            Route::middleware('api')->group(
                __DIR__ . '/../../../routes/api.php'
            );
        }

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
}
