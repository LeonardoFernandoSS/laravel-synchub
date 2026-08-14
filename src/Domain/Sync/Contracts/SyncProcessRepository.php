<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;

interface SyncProcessRepository
{
    public function create(array $data): SyncProcessEntity;

    public function update(
        SyncProcessEntity $process,
        array $data,
    ): bool;

    public function transition(
        SyncProcessEntity $process,
        SyncProcessStatus $from,
        SyncProcessStatus $to,
        array $data = [],
    ): bool;    

    public function findOrFail(int $id): SyncProcessEntity;

    public function find(int $id): ?SyncProcessEntity;

    /**
     * @return SyncProcessEntity[]
     */
    public function findActiveForUpdate(
        string $context,
        int $entityId,
    ): array;

    public function findReusableProcess(
        string $context,
        int $entityId,
    ): ?SyncProcessEntity;
}