<?php

namespace Synchub\LaravelSynchub\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
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
                MakeSynchubCommand::class,
            ]);
        }
    }


    public function boot(): void
    {
        if (
            config('synchub.routes.enabled')
        ) {

            $this->loadRoutesFrom(
                __DIR__ . '/../../../routes/api.php'
            );

            $this->loadRoutesFrom(
                __DIR__ . '/../../../routes/web.php'
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
