<?php

namespace Synchub\LaravelSynchub\Application\Sync\Events;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Illuminate\Foundation\Events\Dispatchable;

final readonly class ProcessSucceeded
{
    use Dispatchable;

    public function __construct(
        public SyncProcessEntity $process,
    ) {}
}
