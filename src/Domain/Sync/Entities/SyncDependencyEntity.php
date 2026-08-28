<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Entities;

use DateTimeInterface;

final class SyncDependencyEntity
{
    public function __construct(
        public readonly int $id,

        public readonly int $processId,

        public readonly int $dependsOnProcessId,

        public readonly string $context,

        public readonly string $source_key,

        public bool $resolved = false,

        public ?DateTimeInterface $resolvedAt = null,
    ) {}

    public function resolve(
        ?DateTimeInterface $resolvedAt = null
    ): void {

        $this->resolved = true;

        $this->resolvedAt = $resolvedAt ?? new \DateTimeImmutable();
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }
}
