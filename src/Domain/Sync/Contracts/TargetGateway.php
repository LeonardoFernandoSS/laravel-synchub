<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncResultData;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncData;
use Synchub\LaravelSynchub\Domain\Sync\Entities\MappingEntity;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\TargetIdentity;

interface TargetGateway
{
    public function create(
        SyncData $data
    ): SyncResultData;

    public function update(
        TargetIdentity $identity,
        SyncData $data
    ): SyncResultData;
}
