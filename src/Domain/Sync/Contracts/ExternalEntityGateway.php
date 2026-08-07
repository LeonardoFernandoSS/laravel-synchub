<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\DTO\ExternalSyncResponse;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncData;

interface ExternalEntityGateway
{
    public function create(
        SyncData $data
    ): ExternalSyncResponse;

    public function update(
        GenericMapping $mapping,
        SyncData $data
    ): ExternalSyncResponse;
}
