<?php

namespace Synchub\LaravelSynchub\Application\Sync\Commands;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessRelationType;

final readonly class StartSync
{
    public function __construct(
        public string $context,
        public mixed $sourceId,
        public bool $force = false,
        public ?SyncProcessEntity $parentProcess = null,
        public ?SyncProcessRelationType $relationType = SyncProcessRelationType::TRIGGERED,
    ) {}
}
