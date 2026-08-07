<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Closure;

class FinishProcessStage implements SyncStage
{
    public function __construct(
        private SyncProcessService $syncProcess
    ) {}

    public function handle(
        SyncExecution $execution,
        Closure $next
    ): mixed {
        $this->syncProcess->success(
            $execution->process,
            $execution->response->raw
        );

        return $next($execution);
    }
}
