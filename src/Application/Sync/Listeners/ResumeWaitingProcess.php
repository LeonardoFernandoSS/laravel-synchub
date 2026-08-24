<?php

namespace Synchub\LaravelSynchub\Application\Sync\Listeners;

use Synchub\LaravelSynchub\Application\Sync\Events\ProcessDependenciesResolved;
use Synchub\LaravelSynchub\Application\Sync\Jobs\ProcessSync;

final class ResumeWaitingProcess
{
    public function handle(
        ProcessDependenciesResolved $event,
    ): void {

        ProcessSync::dispatch(
            $event->process->id,
            config('synchub.queue.tries.default'),
        );
    }
}
