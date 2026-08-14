<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;
use Synchub\LaravelSynchub\Domain\Sync\Exceptions\BusinessException;
use Throwable;

class LoadSourceDataStage implements SyncStage
{
    public function __construct(
        private SyncProcessService $processService,
    ) {}

    public function handle(
        SyncExecution $execution,
        Closure $next,
    ): mixed {
        $process = $execution->process;

        $this->processService->step(
            $process,
            SyncProcessStep::LOAD_SOURCE_DATA,
            SyncProcessMessage::LOADING_SOURCE_DATA,
            [
                "id" => $process->sourceId,
            ]
        );
        
        if ($this->processService->canReuseSourcePayload($process)) {
            $execution->sourcePayload = $process->sourcePayload;

            $this->processService->log(
                $process,
                SyncProcessMessage::SOURCE_PAYLOAD_LOADED_FROM_CACHE,
                [
                    'payload' => $execution->sourcePayload,
                ],
            );

            return $next($execution);
        }

        $execution->sourcePayload = $execution
            ->context
            ->source
            ->find($process->sourceId);

        $this->processService->saveSourcePayload(
            $process,
            $execution->sourcePayload,
        );

        $this->processService->log(
            $process,
            SyncProcessMessage::SOURCE_DATA_LOADED,
            [
                'payload' => $execution->sourcePayload,
            ],
        );

        return $next($execution);
    }
}
