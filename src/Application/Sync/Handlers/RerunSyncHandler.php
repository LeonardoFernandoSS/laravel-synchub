<?php

namespace Synchub\LaravelSynchub\Application\Sync\Handlers;

use Synchub\LaravelSynchub\Application\Sync\Jobs\ProcessSync;
use Synchub\LaravelSynchub\Application\Sync\Services\ProcessLifecycleService;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

final class RerunSyncHandler
{
    public function __construct(
        private SyncProcessService $processService,
        private ProcessLifecycleService $lifecycleService,
    ) {}

    public function handle(
        int $processId,
    ): SyncProcessEntity {
        $process = $this->processService->findOrFail($processId);

        $process = $this->lifecycleService->rerun($process);

        ProcessSync::dispatch(
            $process->id,
            tries: config('synchub.queue.tries.rerun'),
        );

        return $process;
    }
}
