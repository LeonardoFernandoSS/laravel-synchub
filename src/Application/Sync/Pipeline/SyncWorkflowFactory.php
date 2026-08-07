<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline;

use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Facades\Sync;

class SyncWorkflowFactory
{
    public function __construct(
        protected SyncProcessService $syncProcess,
    ) {}

    public function make(
        string $context,
    ): SyncWorkflow {

        return new SyncWorkflow(
            $this->syncProcess,
            Sync::context($context),
        );
    }
}