<?php

namespace Synchub\LaravelSynchub\Domain\Sync\DTO;

final class SyncDependencyData
{
    public function __construct(
        public readonly string $context,
        public readonly int $entityId
    ) {}
}
