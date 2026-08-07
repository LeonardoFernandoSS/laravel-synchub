<?php

namespace Synchub\LaravelSynchub\Infrastructure\Persistence;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncLogRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncLogType;
use Synchub\LaravelSynchub\Domain\Sync\Models\SyncLog;

class EloquentSyncLogRepository implements SyncLogRepository
{
    public function create(
        SyncProcessEntity $process,
        string $message,
        array $payload,
        SyncLogType $type = SyncLogType::TIMELINE,
    ): void {

        SyncLog::create([
            'sync_process_id' => $process->id,
            'step' => $process->step,
            'message' => $message,
            'payload' => $payload,
            'type' => $type,
        ]);
    }
}
