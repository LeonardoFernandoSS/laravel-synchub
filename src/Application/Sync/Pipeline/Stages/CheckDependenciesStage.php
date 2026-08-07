<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Exceptions\MissingSyncDependencyException;

class CheckDependenciesStage implements SyncStage
{
    public function __construct(
        private SyncProcessService $syncProcess,
    ) {}

    public function handle(
        SyncExecution $execution,
        Closure $next
    ): mixed {

        $dependencies =  $execution->context
            ->dependencies
            ?->check(
                $execution->internalPayload
            );

        if (!empty($dependencies)) {
            throw new MissingSyncDependencyException($dependencies);
        }

        return $next($execution);
    }
}
