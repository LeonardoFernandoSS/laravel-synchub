<?php

namespace Synchub\LaravelSynchub\Application\Sync\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Synchub\LaravelSynchub\Application\Sync\Events\DependencyResolved;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessDependenciesResolved;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncDependencyRepository;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncProcessRepository;

final class CheckProcessDependencies implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private SyncDependencyRepository $dependencyRepository,
        private SyncProcessRepository $processRepository,
    ) {}

    public function handle(
        DependencyResolved $event,
    ): void {
        $dependency = $event->dependency;

        if (
            $this->dependencyRepository->existsPendingForProcess(
                $dependency->processId,
            )
        ) {
            return;
        }

        $process = $this->processRepository->findOrFail(
            $dependency->processId,
        );

        ProcessDependenciesResolved::dispatch($process);
    }
}