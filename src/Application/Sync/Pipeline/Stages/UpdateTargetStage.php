<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncResultData;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;

final class UpdateTargetStage implements SyncStage
{
    public function __construct(
        private SyncProcessService $processService,
    ) {}

    public function handle(
        SyncExecution $execution,
        Closure $next,
    ): mixed {
        $process = $execution->process;
        $mapping = $execution->mapping;
        $targetData = $execution->targetData;

        $identity = $mapping->sourceIdentity;

        $this->processService->step(
            $process,
            SyncProcessStep::UPDATE_TARGET,
            SyncProcessMessage::TARGET_RECORD_UPDATING,
            [
                'source_identity' => $identity->values(),
                'target_identity' => $mapping->targetIdentity->values(),
                'payload' => $targetData->payload,
                'payload_hash' => $targetData->hash,
            ],
        );

        if (
            !$process->force &&
            $mapping->payloadHash === $targetData->hash
        ) {
            $execution->targetResponse = new SyncResultData(
                $mapping->targetIdentity,
                $process->targetResponse,
            );

            $this->processService->log(
                $process,
                SyncProcessMessage::SYNC_SKIPPED_NO_CHANGES,
                [
                    'source_identity' => $identity->values(),
                    'target_identity' => $mapping->targetIdentity->values(),
                    'payload_hash' => $targetData->hash,
                    'force' => false,
                ],
            );

            return $next($execution);
        }

        $targetResponse = $execution
            ->context
            ->target
            ->update(
                $mapping->targetIdentity,
                $targetData,
            );

        $execution->targetResponse = $targetResponse;

        $this->processService->saveTargetResponse(
            $process,
            $targetResponse->raw,
        );

        $this->processService->log(
            $process,
            SyncProcessMessage::TARGET_RESPONSE_SAVED,
            [
                'source_identity' => $identity->values(),
                'response' => $targetResponse->raw,
                'target_identity' => $targetResponse->identity,
            ],
        );

        $execution
            ->context
            ->repository
            ->update(
                $mapping,
                $targetResponse,
                $targetData,
            );

        $this->processService->log(
            $process,
            SyncProcessMessage::TARGET_MAPPING_SAVED,
            [
                'source_identity' => $identity->values(),
                'target_identity' => $targetResponse->identity,
                'payload_hash' => $targetData->hash,
            ],
        );

        return $next($execution);
    }
}
