<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncData;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;

class MapDataStage implements SyncStage
{
    public function __construct(
        private SyncProcessService $processService,
    ) {}

    public function handle(
        SyncExecution $execution,
        Closure $next,
    ): mixed {
        $process = $execution->process;
        $sourcePayload = $execution->sourcePayload;

        $this->processService->step(
            $process,
            SyncProcessStep::MAP_DATA,
            SyncProcessMessage::DATA_MAPPING,
        );

        $targetData = $execution
            ->context
            ->mapper
            ?->map(
                $sourcePayload,
                $execution->mapping->lastPayload
            );

        if ($targetData === null) {
            $targetData = new SyncData(
                payload: $sourcePayload,
            );
        }

        $execution->targetData = $targetData;

        $this->processService->saveTargetPayload(
            $process,
            $targetData->payload,
        );

        $this->processService->log(
            $process,
            SyncProcessMessage::DATA_MAPPED,
            [
                'payload' => $targetData->payload,
                'payload_hash' => $targetData->hash,
            ],
        );

        return $next($execution);
    }
}
