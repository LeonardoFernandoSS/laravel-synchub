<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncLogRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncLogType;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;

class ProcessLogService
{
    public function __construct(
        protected SyncLogRepository $logRepository,
    ) {}

    public function log(
        SyncProcessEntity $process,
        SyncProcessMessage|string $message,
        array $payload = [],
        SyncLogType $type = SyncLogType::TIMELINE,
    ): void {
        $this->logRepository->create(
            $process,
            $message instanceof SyncProcessMessage
                ? $message->value
                : $message,
            $payload,
            $type
        );
    }

    public function debug(
        SyncProcessEntity $process,
        SyncProcessMessage|string $message,
        array $payload = []
    ): void {

        $this->log(
            $process,
            $message,
            $payload,
            SyncLogType::DEBUG
        );
    }
}
