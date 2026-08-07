<?php

namespace Synchub\LaravelSynchub\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Synchub\LaravelSynchub\Infrastructure\Registry\SyncRegistry;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            SyncRegistry::class,
            fn () => new SyncRegistry()
        );
    }
}