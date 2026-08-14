<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;

class SynchronizeStage implements SyncStage
{
    public function __construct(
        private SyncProcessService $processService,
        private CreateTargetStage $create,
        private UpdateTargetStage $update,
    ) {}

    public function handle(
        SyncExecution $execution,
        Closure $next,
    ): mixed {

        if (!$this->processService->isStillRunnable(
            $execution->process,
        )) {
            return $execution;
        }

        if ($execution->mapping === null) {
            $this->create->handle(
                $execution,
                fn(SyncExecution $execution) => $execution,
            );
        } else {
            $this->update->handle(
                $execution,
                fn(SyncExecution $execution) => $execution,
            );
        }

        return $next($execution);
    }
}
