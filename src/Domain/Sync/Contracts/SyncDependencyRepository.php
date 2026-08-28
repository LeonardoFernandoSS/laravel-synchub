<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncDependencyEntity;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;

interface SyncDependencyRepository
{
    public function create(
        SyncProcessEntity $process,
        SyncProcessEntity $dependencyProcess,
        string $context,
        SourceIdentity $sourceIdentity
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
