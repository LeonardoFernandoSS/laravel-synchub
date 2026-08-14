<?php

namespace Synchub\LaravelSynchub\Application\Sync\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncDependencyData;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

final readonly class MissingDependenciesDetected
{
    use Dispatchable;

    /**
     * @param SyncDependencyData[] $dependencies
     */
    public function __construct(
        public SyncProcessEntity $process,
        public array $dependencies,
    ) {}
}
