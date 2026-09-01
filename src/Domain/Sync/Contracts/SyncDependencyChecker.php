<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncDependencyData;

interface SyncDependencyChecker
{
    /**
     * @return SyncDependencyData[]
     */
    public function check(array $data): array;
}
