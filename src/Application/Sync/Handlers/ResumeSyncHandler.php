<?php

namespace Synchub\LaravelSynchub\Application\Sync\Handlers;

use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncWorkflowFactory;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

final class ResumeSyncHandler
{
    public function __construct(
        private SyncWorkflowFactory $workflowFactory,
    ) {}

    public function handle(
        SyncProcessEntity $process,
    ): void {
        
        if (!$process->isWaitingDependency()) {
            return;
        }

        $this->workflowFactory
            ->make($process->context)
            ->resume($process->id);
    }
}
