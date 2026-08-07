<?php

namespace Synchub\LaravelSynchub\Application\Sync\Handlers;

use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncWorkflowFactory;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

class RunSyncWorkflowHandler
{
    public function __construct(
        private SyncWorkflowFactory $workflowFactory,
    ) {}

    public function handle(SyncProcessEntity $process): void
    {
        $this->workflowFactory
            ->make($process->context)
            ->resume($process->id);
    }
}
