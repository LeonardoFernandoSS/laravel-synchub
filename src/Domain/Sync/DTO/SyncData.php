<?php

namespace Synchub\LaravelSynchub\Domain\Sync\DTO;

class SyncData
{
    public function __construct(
        public readonly string $target,
        public readonly array $payload,
        public readonly string $hash
    ) {}
}
