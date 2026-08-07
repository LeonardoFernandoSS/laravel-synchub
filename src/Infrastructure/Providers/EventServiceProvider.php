<?php

namespace Synchub\LaravelSynchub\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Synchub\LaravelSynchub\Application\Sync\Events\DependencyResolved;
use Synchub\LaravelSynchub\Application\Sync\Events\MissingDependenciesDetected;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessDependenciesResolved;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessSucceeded;
use Synchub\LaravelSynchub\Application\Sync\Listeners\CheckDependenciesAfterResolution;
use Synchub\LaravelSynchub\Application\Sync\Listeners\ExecuteAfterSync;
use Synchub\LaravelSynchub\Application\Sync\Listeners\HandleMissingDependencies;
use Synchub\LaravelSynchub\Application\Sync\Listeners\ResolveWaitingDependencies;
use Synchub\LaravelSynchub\Application\Sync\Listeners\ResumeWaitingProcess;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        MissingDependenciesDetected::class => [
            HandleMissingDependencies::class,
        ],

        ProcessSucceeded::class => [
            ExecuteAfterSync::class,
            ResolveWaitingDependencies::class,
        ],

        DependencyResolved::class => [
            CheckDependenciesAfterResolution::class,
        ],

        ProcessDependenciesResolved::class => [
            ResumeWaitingProcess::class,
        ],

    ];
}
