<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;

class LoadInternalDataStage implements SyncStage
{
    public function __construct(
        private SyncProcessService $syncProcess,
    ) {}

    public function handle(
        SyncExecution $execution,
        Closure $next
    ): mixed {

        $process = $execution->process;

        $this->syncProcess->step(
            $process,
            SyncProcessStep::LOAD_DATA,
            'Carregando dados'
        );

        if (
            $this->syncProcess->canReuseInternalPayload($process)
        ) {

            $execution->internalPayload =
                $this->syncProcess->getInternalPayload($process);

            return $next($execution);
        }

        $execution->internalPayload =
            $execution->context
            ->internal
            ->find($process->contextId);

        $this->syncProcess->saveInternalPayload(
            $process,
            $execution->internalPayload
        );

        return $next($execution);
    }
}
