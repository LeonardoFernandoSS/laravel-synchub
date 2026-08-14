<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Entities;

final class MappingEntity
{
    public function __construct(
        public readonly string $context,
        public readonly mixed $sourceId,
        public readonly mixed $targetId,
        public readonly string $payloadHash,
    ) {}
}
