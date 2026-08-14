<?php

namespace Synchub\LaravelSynchub\Application\Sync\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

final readonly class ProcessDependenciesResolved
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public SyncProcessEntity $process,
    ) {}
}