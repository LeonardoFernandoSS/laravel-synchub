<?php

namespace Synchub\LaravelSynchub\Application\Sync\Handlers;

use Synchub\LaravelSynchub\Application\Sync\Commands\StartSync;
use Synchub\LaravelSynchub\Application\Sync\Jobs\ProcessSync;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

final class StartSyncHandler
{
    public function __construct(
        private CreateSyncProcessHandler $createProcess,
    ) {}

    public function handle(
        StartSync $command,
    ): SyncProcessEntity {
        $process = $this->createProcess->handle(
            $command,
        );

        ProcessSync::dispatch(
            $process->id,
            config('synchub.queue.tries.default'),
        );

        return $process;
    }
}
