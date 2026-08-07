<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;

class CreateExternalStage
{
    public function __construct(
        private SyncProcessService $syncProcess,
    ) {}

    public function execute(
        SyncExecution $execution
    ): void {

        $process = $execution->process;

        $mapped = $execution->mappedData;

        $this->syncProcess->step(
            $process,
            SyncProcessStep::CREATE_EXTERNAL,
            SyncProcessMessage::CREATING_EXTERNAL_RECORD
        );

        $response = $execution->context
            ->external
            ->create($mapped);

        $execution->response = $response;

        $this->syncProcess->saveExternalResponse(
            $process,
            $response->raw
        );

        $execution->context
            ->mapping
            ->create(
                $process->context,
                $process->contextId,
                $response->id,
                $mapped->target,
                $mapped->hash
            );

        $execution->context
            ->internal
            ->saveExternalId(
                $process->contextId,
                $response->id
            );
    }
}
