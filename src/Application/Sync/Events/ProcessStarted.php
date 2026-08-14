<?php

namespace Synchub\LaravelSynchub\Application\Sync\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

final readonly class ProcessStarted
{
    use Dispatchable;

    public function __construct(
        public SyncProcessEntity $process,
    ) {}
}
