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
        $idChunks = array_chunk(
            $command->ids,
            $command->chunkSize
        );

        $jobs = collect($idChunks)
            ->map(fn (array $ids) => new ProcessBatchSync(
                context: $command->context,
                ids: $ids,
                force: $command->force,
                parentProcess: $command->parentProcess,
            ))
            ->all();

        Bus::batch($jobs)
            ->name("Sync {$command->context}")
            ->dispatch();
    }
}