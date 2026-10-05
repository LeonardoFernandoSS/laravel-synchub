<?php

namespace Synchub\LaravelSynchub\Application\Sync\Commands;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessRelationType;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;

final readonly class StartSync
{
    public function __construct(
        public string $context,
        public SourceIdentity $identity,
        public bool $force = false,
        public ?array $sourcePayload = null,
        public ?SyncProcessEntity $parentProcess = null,
        public ?SyncProcessRelationType $relationType = SyncProcessRelationType::TRIGGERED,
    ) {}
}
