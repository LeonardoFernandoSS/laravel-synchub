<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncResultData;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncData;
use Synchub\LaravelSynchub\Domain\Sync\Entities\MappingEntity;

interface MappingRepository
{
    public function findBySourceId(
        string $sourceType,
        int $sourceId,
    ): ?MappingEntity;

    public function create(
        string $sourceType,
        int $sourceId,
        SyncResultData $response,
        SyncData $mappedData,
    ): MappingEntity;

    public function update(
        MappingEntity $mapping,
        SyncResultData $response,
        SyncData $mappedData,
    ): MappingEntity;

    public function existByInternalId(
        string $mappableType,
        int $sourceId
    ): bool;

    public function getExternalIdByInternalId(
        string $mappableType,
        int $sourceId
    ): ?string;
}
