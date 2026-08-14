<?php

namespace Synchub\LaravelSynchub\Application\Sync\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessSucceeded;
use Synchub\LaravelSynchub\Infrastructure\Registry\SyncRegistry;

final class RunAfterSync implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private SyncRegistry $registry,
    ) {}

    public function handle(
        ProcessSucceeded $event,
    ): void {
        $context = $this->registry->context(
            $event->process->context,
        );

        $context->afterSync?->handle(
            $event->process,
        );
    }
}