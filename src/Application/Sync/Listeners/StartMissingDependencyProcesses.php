<?php

namespace Synchub\LaravelSynchub\Application\Sync\Listeners;

use Synchub\LaravelSynchub\Application\Sync\Commands\StartSync;
use Synchub\LaravelSynchub\Application\Sync\Events\MissingDependenciesDetected;
use Synchub\LaravelSynchub\Application\Sync\Handlers\CreateSyncProcessHandler;
use Synchub\LaravelSynchub\Application\Sync\Jobs\ProcessSync;
use Synchub\LaravelSynchub\Application\Sync\Services\ProcessFinderService;
use Synchub\LaravelSynchub\Application\Sync\Services\ProcessLogService;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncDependencyService;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessRelationType;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;

final class StartMissingDependencyProcesses
{
    public function __construct(
        private CreateSyncProcessHandler $createProcess,
        private ProcessFinderService $finder,
        private SyncDependencyService $dependencyService,
        private ProcessLogService $logService,
    ) {}

    public function handle(
        MissingDependenciesDetected $event,
    ): void {

        foreach ($event->dependencies as $dependency) {

            $dependencyProcess = $this->finder->findReusableProcess(
                context: $dependency->context,
                entityId: $dependency->entityId,
            );

            $wasReused = $dependencyProcess !== null;

            if (!$dependencyProcess) {
                $dependencyProcess = $this->createProcess->handle(
                    new StartSync(
                        context: $dependency->context,
                        id: $dependency->entityId,
                        force: false,
                        parentProcess: $event->process,
                        relationType: SyncProcessRelationType::DEPENDENCY,
                    ),
                );
            }

            $this->logService->log(
                $dependencyProcess,
                $wasReused
                    ? SyncProcessMessage::DEPENDENCY_PROCESS_REUSED
                    : SyncProcessMessage::DEPENDENCY_PROCESS_CREATED,
                [
                    'parent_process_id' => $event->process->id,
                    'context' => $dependency->context,
                    'entity_id' => $dependency->entityId,
                ],
            );

            $this->dependencyService->addDependency(
                process: $event->process,
                dependencyProcess: $dependencyProcess,
                context: $dependency->context,
                entityId: $dependency->entityId,
            );

            $this->logService->log(
                $dependencyProcess,
                SyncProcessMessage::DEPENDENCY_REGISTERED,
                [
                    'process_id' => $dependencyProcess->id,
                    'dependent_process_id' => $event->process->id,
                    'context' => $dependency->context,
                    'entity_id' => $dependency->entityId,
                ],
            );

            if (
                $dependencyProcess->status !== SyncProcessStatus::PENDING
            ) {
                continue;
            }

            ProcessSync::dispatch(
                $dependencyProcess->id,
                config('synchub.queue.tries.dependency')
            );
        }
    }
}
