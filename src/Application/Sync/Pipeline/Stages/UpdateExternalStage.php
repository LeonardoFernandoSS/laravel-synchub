<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\DTO\ExternalSyncResponse;

class UpdateExternalStage
{
    public function __construct(
        private SyncProcessService $syncProcess,
    ) {}

    public function execute(
        SyncExecution $execution
    ): void {

        $process = $execution->process;

        $mapping = $execution->mapping;

        $mapped = $execution->mappedData;

        if (
            !$process->force &&
            $mapping->payloadHash() === $mapped->hash
        ) {

            $execution->response =
                new ExternalSyncResponse(
                    $mapping->externalId(),
                    $process->externalResponse
                );

            return;
        }

        $response = $execution->context
            ->external
            ->update(
                $mapping,
                $mapped
            );

        $execution->response = $response;

        $this->syncProcess->saveExternalResponse(
            $process,
            $response->raw
        );
    }
}
