<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Entities;

final class MappingEntity
{
    public function __construct(
        public readonly string $context,
        public readonly int $entityId,
        public readonly string $targetId,
        public readonly string $payloadHash,
    ) {}
}
