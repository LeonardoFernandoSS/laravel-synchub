<?php

namespace Synchub\LaravelSynchub\Application\Sync\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Synchub\LaravelSynchub\Application\Sync\Handlers\ProcessSyncHandler;
use Synchub\LaravelSynchub\Application\Sync\Handlers\ResumeSyncHandler;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Throwable;

final class ResumeProcessSync implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $processId,
        public int $tries = 3,
    ) {}

    public function handle(
        ResumeSyncHandler $handler,
        SyncProcessService $processService,
    ): void {
        $process = $processService->findOrFail(
            $this->processId,
        );

        if (!$process->isRunnable()) {
            return;
        }

        $handler->handle($process);
    }

    public function failed(?Throwable $exception): void
    {
        $processService = app(SyncProcessService::class);

        $process = $processService->find(
            $this->processId,
        );

        if (!$process) {
            return;
        }

        if (!$process->isRunnable()) {
            return;
        }

        $processService->error(
            $process,
            $exception,
        );
    }

    public function backoff(): array
    {
        return config('synchub.queue.backoff');
    }
}
