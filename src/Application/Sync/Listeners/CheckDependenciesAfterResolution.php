<?php

namespace Synchub\LaravelSynchub\Application\Sync\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Synchub\LaravelSynchub\Application\Sync\Events\DependencyResolved;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessDependenciesResolved;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncDependencyRepository;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncProcessRepository;

class CheckDependenciesAfterResolution implements ShouldQueue
{
    use Queueable;
    
    public function __construct(
        private SyncDependencyRepository $dependencyRepository,
        private SyncProcessRepository $processRepository,
    ) {}

    public function handle(
        DependencyResolved $event
    ): void {

        $dependency = $event->dependency;


        $hasPendingDependencies = $this->dependencyRepository
            ->existsPendingForProcess(
                $dependency->syncProcessId
            );


        if ($hasPendingDependencies) {
            return;
        }


        $process = $this->processRepository
            ->findOrFail(
                $dependency->syncProcessId
            );


        ProcessDependenciesResolved::dispatch(
            $process
        );
    }
}
