<?php

namespace Synchub\LaravelSynchub\Application\Sync\Handlers;

use Synchub\LaravelSynchub\Application\Sync\Commands\ResumeSync;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncWorkflowFactory;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;

class ResumeSyncHandler
{

    public function __construct(
        private SyncProcessService $processService,
        private SyncWorkflowFactory $workflowFactory,
    ) {}


    public function handle(
        ResumeSync $command
    ): void {

        $process = $this->processService
            ->findOrFail(
                $command->processId
            );


        $workflow = $this->workflowFactory->make(
            $process->context
        );


        $workflow->resume(
            $process->id
        );
    }
}
