<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;

class CreateTargetStage implements SyncStage
{
    public function __construct(
        private SyncProcessService $processService,
    ) {}

    public function handle(
        SyncExecution $execution,
        Closure $next,
    ): mixed {
        $process = $execution->process;
        $targetData = $execution->targetData;

        $this->processService->step(
            $process,
            SyncProcessStep::CREATE_TARGET,
            SyncProcessMessage::TARGET_RECORD_CREATING,
            [
                'payload' => $targetData->payload,
            ],
        );

        $response = $execution
            ->context
            ->target
            ->create($targetData);

        $execution->targetResponse = $response;

        $this->processService->saveTargetResponse(
            $process,
            $response->raw,
        );

        $execution->context
            ->repository
            ->create(
                $process->context,
                $process->entityId,
                $response,
                $targetData,
            );

        $this->processService->log(
            $process,
            SyncProcessMessage::TARGET_MAPPING_SAVED,
            [
                'target_id' => $response->id,
                'payload_hash' => $targetData->hash,
            ],
        );

        $execution->context
            ->source
            ->saveExternalId(
                $process->entityId,
                $response->id,
            );

        $this->processService->log(
            $process,
            SyncProcessMessage::TARGET_ID_SENT_TO_SOURCE,
            [
                'target_id' => $response->id,
            ],
        );

        return $next($execution);
    }
}
