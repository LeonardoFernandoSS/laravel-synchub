<?php

namespace Synchub\LaravelSynchub\Application\Sync\Handlers;

use Synchub\LaravelSynchub\Application\Sync\Commands\StartBatchSync;
use Synchub\LaravelSynchub\Jobs\DispatchBatchSync;

class StartBatchSyncHandler
{

    public function handle(
        StartBatchSync $command
    ): void {
        DispatchBatchSync::dispatch(
            context: $command->context,
            ids: $command->ids,
            force: $command->force,
            parentProcess: $command->parentProcess,
        );
    }
}
