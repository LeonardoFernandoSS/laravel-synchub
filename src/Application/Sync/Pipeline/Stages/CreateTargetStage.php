<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;

final class CreateTargetStage implements SyncStage
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
        $identity = $process->sourceIdentity;

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
                context: $process->context,
                identity: $identity,
                response: $response,
                mappedData: $targetData,
            );

        $this->processService->log(
            $process,
            SyncProcessMessage::TARGET_MAPPING_SAVED,
            [
                'source_identity' => $identity->values(),
                'target_identity' => $response->identity->values(),
                'payload_hash' => $targetData->hash,
            ],
        );

        $execution->context
            ->source
            ->saveTargetId(
                identity: $identity,
                targetIdentity: $response->identity,
            );

        $this->processService->log(
            $process,
            SyncProcessMessage::TARGET_ID_SENT_TO_SOURCE,
            [
                'source_identity' => $identity->values(),
                'target_identity' => $response->identity->values(),
            ],
        );

        return $next($execution);
    }
}
