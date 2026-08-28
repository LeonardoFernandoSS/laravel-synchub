<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Entities;

use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\TargetIdentity;

final class MappingEntity
{
    public function __construct(
        public readonly string $context,
        public readonly SourceIdentity $sourceIdentity,
        public readonly ?TargetIdentity $targetIdentity,
        public readonly string $payloadHash,
        public readonly array $lastPayload = [],
    ) {}
}
