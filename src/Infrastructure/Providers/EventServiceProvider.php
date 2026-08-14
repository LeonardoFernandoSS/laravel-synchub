<?php

namespace Synchub\LaravelSynchub\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Synchub\LaravelSynchub\Application\Sync\Events\DependencyResolved;
use Synchub\LaravelSynchub\Application\Sync\Events\MissingDependenciesDetected;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessDependenciesResolved;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessSucceeded;
use Synchub\LaravelSynchub\Application\Sync\Listeners\CheckDependenciesAfterResolution;
use Synchub\LaravelSynchub\Application\Sync\Listeners\CheckProcessDependencies;
use Synchub\LaravelSynchub\Application\Sync\Listeners\ExecuteAfterSync;
use Synchub\LaravelSynchub\Application\Sync\Listeners\ResolveWaitingDependencies;
use Synchub\LaravelSynchub\Application\Sync\Listeners\ResumeWaitingProcess;
use Synchub\LaravelSynchub\Application\Sync\Listeners\RunAfterSync;
use Synchub\LaravelSynchub\Application\Sync\Listeners\StartMissingDependencyProcesses;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        MissingDependenciesDetected::class => [
            StartMissingDependencyProcesses::class,
        ],

        ProcessSucceeded::class => [
            RunAfterSync::class,
            ResolveWaitingDependencies::class,
        ],

        DependencyResolved::class => [
            CheckProcessDependencies::class,
        ],

        ProcessDependenciesResolved::class => [
            ResumeWaitingProcess::class,
        ],

    ];
}
