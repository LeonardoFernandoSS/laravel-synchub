<?php

namespace Synchub\LaravelSynchub\Application\Sync\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessSucceeded;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncDependencyService;

class ResolveWaitingDependencies implements ShouldQueue
{
    use Queueable;
    
    public function __construct(
        private SyncDependencyService $dependencies
    ) {}

    public function handle(
        ProcessSucceeded $event
    )
    {
        $this->dependencies
            ->resolve(
                $event->process
            );
    }
}
