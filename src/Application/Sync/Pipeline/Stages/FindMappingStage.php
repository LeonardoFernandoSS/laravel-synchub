<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Closure;

class FindMappingStage implements SyncStage
{
    public function handle(
        SyncExecution $execution,
        Closure $next
    ): mixed {
        $execution->mapping =
            $execution->context
            ->mapping
            ->findByInternalId(
                $execution->process->context,
                $execution->process->contextId
            );

        return $next($execution);
    }
}
