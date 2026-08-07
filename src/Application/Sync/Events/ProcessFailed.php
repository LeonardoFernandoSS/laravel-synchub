<?php

namespace Synchub\LaravelSynchub\Application\Sync\Events;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Throwable;

final readonly class ProcessFailed
{
    public function __construct(
        public SyncProcessEntity $process,
        public Throwable $exception,
    ) {}
}
