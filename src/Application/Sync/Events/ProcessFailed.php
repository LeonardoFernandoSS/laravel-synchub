<?php

namespace Synchub\LaravelSynchub\Application\Sync\Events;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

final readonly class ProcessFailed
{
    public function __construct(
        public SyncProcessEntity $process,
        public string $message,
        public array $errors = [],
    ) {}
}