<?php

namespace Synchub\LaravelSynchub\Application\Sync\Handlers;

use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncWorkflowFactory;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

final class ProcessSyncHandler
{
    public function __construct(
        private SyncWorkflowFactory $workflowFactory,
    ) {}

    public function handle(
        SyncProcessEntity $process,
    ): void {
        
        if (!$process->isRunnable()) {
            return;
        }

        $this->workflowFactory
            ->make($process->context)
            ->resume($process->id);
    }
}