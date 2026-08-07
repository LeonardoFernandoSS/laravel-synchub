<?php

namespace Synchub\LaravelSynchub\Application\Sync\Handlers;

use Synchub\LaravelSynchub\Application\Sync\Commands\StartSync;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

class StartSyncHandler
{
    public function __construct(
        private CreateSyncProcessHandler $createProcess,
        private RunSyncWorkflowHandler $runWorkflow,
    ) {}

    public function handle(
        StartSync $command
    ): SyncProcessEntity {

        $process = $this->createProcess->handle($command);

        $this->runWorkflow->handle($process);

        return $process;
    }
}
