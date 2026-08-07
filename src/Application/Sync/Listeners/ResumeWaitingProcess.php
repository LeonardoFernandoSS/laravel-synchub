<?php

namespace Synchub\LaravelSynchub\Application\Sync\Listeners;

use Synchub\LaravelSynchub\Application\Sync\Commands\ResumeSync;
use Synchub\LaravelSynchub\Application\Sync\Handlers\ResumeSyncHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessDependenciesResolved;

class ResumeWaitingProcess implements ShouldQueue
{
    use Queueable;
    
    public function __construct(
        private ResumeSyncHandler $handler
    ) {}


    public function handle(
        ProcessDependenciesResolved $event
    ): void {

        $this->handler->handle(
            new ResumeSync(
                $event->process->id
            )
        );
    }
}
