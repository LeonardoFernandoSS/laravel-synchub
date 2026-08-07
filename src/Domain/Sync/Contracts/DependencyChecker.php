<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncDependencyData;

interface DependencyChecker
{
    /**
     * @return SyncDependencyData[]
     */
    public function check(array $internalPayload): array;
}
