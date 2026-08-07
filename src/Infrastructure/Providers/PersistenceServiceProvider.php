<?php

namespace Synchub\LaravelSynchub\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\MappingRepository;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncDependencyRepository;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncLogRepository;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncProcessRepository;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncRelationRepository;
use Synchub\LaravelSynchub\Infrastructure\Persistence\EloquentMappingRepository;
use Synchub\LaravelSynchub\Infrastructure\Persistence\EloquentSyncDependencyRepository;
use Synchub\LaravelSynchub\Infrastructure\Persistence\EloquentSyncLogRepository;
use Synchub\LaravelSynchub\Infrastructure\Persistence\EloquentSyncProcessRepository;
use Synchub\LaravelSynchub\Infrastructure\Persistence\EloquentSyncRelationRepository;

class PersistenceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            SyncProcessRepository::class,
            EloquentSyncProcessRepository::class
        );

        $this->app->bind(
            SyncLogRepository::class,
            EloquentSyncLogRepository::class
        );

        $this->app->bind(
            SyncDependencyRepository::class,
            EloquentSyncDependencyRepository::class
        );

        $this->app->bind(
            SyncRelationRepository::class,
            EloquentSyncRelationRepository::class
        );

        $this->app->bind(
            MappingRepository::class,
            EloquentMappingRepository::class
        );
    }
}
