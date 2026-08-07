<?php

namespace Synchub\LaravelSynchub\Application\Sync\Listeners;

use Synchub\LaravelSynchub\Application\Sync\Commands\StartSync;
use Synchub\LaravelSynchub\Application\Sync\Events\MissingDependenciesDetected;
use Synchub\LaravelSynchub\Application\Sync\Handlers\CreateSyncProcessHandler;
use Synchub\LaravelSynchub\Application\Sync\Handlers\RunSyncWorkflowHandler;
use Synchub\LaravelSynchub\Application\Sync\Services\ProcessFinderService;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncDependencyService;

class HandleMissingDependencies
{
    public function __construct(
        private CreateSyncProcessHandler $createProcess,
        private RunSyncWorkflowHandler $runWorkflow,
        private ProcessFinderService $finder,
        private SyncDependencyService $dependencyService,
    ) {}

    public function handle(MissingDependenciesDetected $event): void
    {
        foreach ($event->dependencies as $dependency) {

            $dependencyProcess = $this->finder->findReusableProcess(
                $dependency->context,
                $dependency->contextId
            );

            if (!$dependencyProcess) {

                $dependencyProcess = $this->createProcess->handle(
                    new StartSync(
                        context: $dependency->context,
                        id: $dependency->contextId,
                        force: false,
                        parentProcess: $event->process,
                    )
                );
            }

            $this->dependencyService->addDependency(
                process: $event->process,
                dependencyProcess: $dependencyProcess,
                context: $dependency->context,
                contextId: $dependency->contextId,
            );

            $this->runWorkflow->handle($dependencyProcess);
        }
    }
}
