<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Entities;

use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;

final class SyncProcessEntity
{
    public function __construct(
        public ?int $id,

        public string $context,

        public mixed $sourceId,

        public SyncProcessStatus $status,

        public SyncProcessStep $currentStep,

        public bool $force,

        public array $sourcePayload = [],

        public array $targetPayload = [],

        public array $targetResponse = [],

        public array $error = [],

        public ?\DateTimeInterface $payloadCachedAt = null,

        public ?\DateTimeInterface $startedAt = null,
        public ?\DateTimeInterface $resumedAt = null,
        public ?\DateTimeInterface $finishedAt = null,
    ) {}

    public function isWaitingDependency(): bool
    {
        return $this->status === SyncProcessStatus::WAITING_DEPENDENCY;
    }

    public function isRunnable(): bool
    {
        return in_array(
            $this->status,
            [
                SyncProcessStatus::PENDING,
                SyncProcessStatus::PROCESSING,
                SyncProcessStatus::WAITING_DEPENDENCY,
            ],
            true,
        );
    }
}
