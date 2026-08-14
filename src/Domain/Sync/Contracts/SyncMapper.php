<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncData;

interface SyncMapper
{
    public function map(array $sourcePayload): SyncData;
}
