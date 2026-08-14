<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncLogType;

interface SyncLogRepository
{
    public function create(
        SyncProcessEntity $process,
        string $message,
        array $payload,
        SyncLogType $type,
    ): void;
}