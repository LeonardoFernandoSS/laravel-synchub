<?php

namespace Synchub\LaravelSynchub\Application\Sync\Commands;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;


final readonly class StartBatchSync
{
    public function __construct(
        public string $context,
        public array $ids,
        public bool $force = false,
        public ?SyncProcessEntity $parentProcess = null,
    ) {}
}
