<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

interface SourceGateway
{
    public function find(mixed $sourceId): ?array;

    public function saveTargetId(
        mixed $sourceId,
        mixed $targetId
    ): void;
}
