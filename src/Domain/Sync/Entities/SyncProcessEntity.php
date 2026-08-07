<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Entities;

use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;

class SyncProcessEntity
{
    public function __construct(

        public ?int $id,

        public string $type,

        public string $context,

        public int $contextId,

        public SyncProcessStatus $status = SyncProcessStatus::PENDING,

        public SyncProcessStep $step = SyncProcessStep::CREATED,

        public bool $force = false,

        public array $internalPayload = [],

        public array $mappedPayload = [],

        public array $externalResponse = [],

        public array $error = [],

        public ?\DateTimeInterface $payloadCachedAt = null,

    ) {}
}
