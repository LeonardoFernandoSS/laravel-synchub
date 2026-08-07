<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

interface SyncProcessRepository
{
    public function create(array $data): SyncProcessEntity;


    public function update(
        SyncProcessEntity $process,
        array $data
    ): bool;


    public function findOrFail(
        int $id
    ): SyncProcessEntity;

    public function findReusableProcess(
        string $context,
        int $contextId
    ): ?SyncProcessEntity;

    public function obsoleteRunning(
        string $type,
        string $context,
        int $contextId
    ): void;
}
