<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncResultData;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncData;
use Synchub\LaravelSynchub\Domain\Sync\Entities\MappingEntity;

interface MappingRepository
{
    public function findBySourceId(
        string $sourceType,
        mixed $sourceId,
    ): ?MappingEntity;

    public function create(
        string $sourceType,
        mixed $sourceId,
        SyncResultData $response,
        SyncData $mappedData,
    ): MappingEntity;

    public function update(
        MappingEntity $mapping,
        SyncResultData $response,
        SyncData $mappedData,
    ): MappingEntity;
}
