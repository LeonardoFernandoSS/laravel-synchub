<?php

namespace Synchub\LaravelSynchub\Application\Sync\Events;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

final readonly class ProcessStarted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public SyncProcessEntity $process,
    ) {}
}
