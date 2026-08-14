<?php

namespace Synchub\LaravelSynchub\Application\Sync\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessSucceeded;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncDependencyService;

final class ResolveWaitingDependencies implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private SyncDependencyService $dependencyService,
    ) {}

    public function handle(
        ProcessSucceeded $event,
    ): void {
        
        $this->dependencyService->resolve(
            $event->process,
        );
    }
}
