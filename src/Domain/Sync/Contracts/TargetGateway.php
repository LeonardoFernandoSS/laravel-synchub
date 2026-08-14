<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncResultData;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncData;
use Synchub\LaravelSynchub\Domain\Sync\Entities\MappingEntity;

interface TargetGateway
{
    public function create(
        SyncData $data
    ): SyncResultData;

    public function update(
        MappingEntity $mapping,
        SyncData $data
    ): SyncResultData;
}
