<?php

namespace Synchub\LaravelSynchub\Application\Sync\Handlers;

use Illuminate\Support\Facades\Bus;
use Synchub\LaravelSynchub\Application\Sync\Commands\StartBatchSync;
use Synchub\LaravelSynchub\Application\Sync\Jobs\ProcessBatchSync;

final class StartBatchSyncHandler
{
    public function handle(
        StartBatchSync $command,
    ): void {
        $identityChunks = array_chunk(
            $command->identities,
            $command->chunkSize,
        );

        $jobs = collect($identityChunks)
            ->map(
                fn(array $identities) => new ProcessBatchSync(
                    context: $command->context,
                    identities: $identities,
                    force: $command->force,
                    parentProcess: $command->parentProcess,
                ),
            )
            ->all();

        Bus::batch($jobs)
            ->name("Sync {$command->context}")
            ->dispatch();
    }
}
