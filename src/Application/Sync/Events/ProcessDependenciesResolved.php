<?php

namespace Synchub\LaravelSynchub\Application\Sync\Events;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final readonly class ProcessDependenciesResolved
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public SyncProcessEntity $process,
    ) {}
}