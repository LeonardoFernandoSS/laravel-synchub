<?php

namespace Synchub\LaravelSynchub\Application\Sync\Events;

use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncDependencyData;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Illuminate\Foundation\Events\Dispatchable;

class MissingDependenciesDetected
{
    use Dispatchable;

    /**
     * @param SyncDependencyData[] $dependencies
     */
    public function __construct(
        public readonly SyncProcessEntity $process,
        public readonly array $dependencies,
    ) {}
}
