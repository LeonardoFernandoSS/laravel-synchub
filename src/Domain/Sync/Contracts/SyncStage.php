<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Closure;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;

interface SyncStage
{
    public function handle(
        SyncExecution $execution,
        Closure $next,
    ): mixed;
}
