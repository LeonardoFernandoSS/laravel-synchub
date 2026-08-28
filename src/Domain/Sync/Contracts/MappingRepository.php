<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncData;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncResultData;
use Synchub\LaravelSynchub\Domain\Sync\Entities\MappingEntity;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;

interface MappingRepository
{
    public function find(
        string $context,
        SourceIdentity $identity,
    ): ?MappingEntity;

    public function create(
        string $context,
        SourceIdentity $identity,
        SyncResultData $response,
        SyncData $mappedData,
    ): MappingEntity;

    public function update(
        MappingEntity $mapping,
        SyncResultData $response,
        SyncData $mappedData,
    ): MappingEntity;
}
