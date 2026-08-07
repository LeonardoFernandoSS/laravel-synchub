<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;

class ValidateDataStage implements SyncStage
{
    public function handle(
        SyncExecution $execution,
        Closure $next
    ): mixed {
        $execution->context
            ->validator
            ->validate(
                $execution->internalPayload
            );

        return $next($execution);
    }
}
