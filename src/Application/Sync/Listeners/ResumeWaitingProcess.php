<?php

namespace Synchub\LaravelSynchub\Application\Sync\Listeners;

use Synchub\LaravelSynchub\Application\Sync\Events\ProcessDependenciesResolved;
use Synchub\LaravelSynchub\Application\Sync\Jobs\ResumeProcessSync;

final class ResumeWaitingProcess
{
    public function handle(
        ProcessDependenciesResolved $event,
    ): void {

        ResumeProcessSync::dispatch(
            $event->process->id,
            config('synchub.queue.tries.default'),
        );
    }
}
