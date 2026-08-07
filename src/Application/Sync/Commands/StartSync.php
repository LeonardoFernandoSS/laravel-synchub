<?php

namespace Synchub\LaravelSynchub\Application\Sync\Commands;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

final readonly class StartSync
{
    public function __construct(
        public string $context,
        public int $id,
        public bool $force = false,
        public ?SyncProcessEntity $parentProcess = null,
    ) {}
}
