<?php

namespace Synchub\LaravelSynchub\Application\Sync\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncDependencyEntity;

final readonly class DependencyResolved
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public SyncDependencyEntity $dependency,
    ) {}
}