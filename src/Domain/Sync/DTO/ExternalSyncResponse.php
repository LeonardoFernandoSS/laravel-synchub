<?php

namespace Synchub\LaravelSynchub\Domain\Sync\DTO;

class ExternalSyncResponse 
{
    public function __construct(
        public readonly string $id,
        public readonly array $raw
    ) {}
}
