<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;

class MapDataStage implements SyncStage
{
    public function __construct(
        private SyncProcessService $syncProcess,
    ) {}
    
    public function handle(
        SyncExecution $execution,
        Closure $next
    ): mixed {

        $mappedData = $execution
            ->context
            ->mapper
            ->map(
                $execution->internalPayload
            );


        $execution->mappedData = $mappedData;


        $this->syncProcess
            ->saveMappedPayload(
                $execution->process,
                $mappedData->payload
            );


        $this->syncProcess
            ->log(
                $execution->process,
                SyncProcessMessage::DATA_MAPPED,
                [
                    'payload_hash' => $mappedData->hash,
                    'target' => $mappedData->target,
                ]
            );


        return $next($execution);
    }
}
