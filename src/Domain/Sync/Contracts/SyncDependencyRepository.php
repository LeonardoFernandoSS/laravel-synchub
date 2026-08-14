<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncDependencyEntity;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

interface SyncDependencyRepository
{
    public function create(
        SyncProcessEntity $process,
        SyncProcessEntity $dependencyProcess,
        string $context,
        mixed $sourceId
    ): SyncDependencyEntity;

    /**
     * @return SyncDependencyEntity[]
     */
    public function findPendingByDependencyProcess(
        int $processId
    ): array;

    public function resolve(
        SyncDependencyEntity $dependency
    ): void;

    public function existsPendingForProcess(
        int $processId
    ): bool;
}
