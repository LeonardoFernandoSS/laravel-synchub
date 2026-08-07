<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;

class SynchronizeStage implements SyncStage
{
    public function __construct(
        private CreateExternalStage $create,
        private UpdateExternalStage $update,
    ) {}

    public function handle(
        SyncExecution $execution,
        Closure $next
    ): mixed {

        if (!$execution->mapping) {
            $this->create->execute($execution);
        } else {
            $this->update->execute($execution);
        }

        return $next($execution);
    }
}
