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
                fn (array $identities): ProcessBatchSync =>
                    $this->createJob($command, $identities),
            )
            ->all();

        Bus::batch($jobs)
            ->name("Sync {$command->context}")
            ->dispatch();
    }

    private function createJob(
        StartBatchSync $command,
        array $identities,
    ): ProcessBatchSync {
        return new ProcessBatchSync(
            context: $command->context,
            identities: $identities,
            force: $command->force,
            sourcePayloads: $this->getSourcePayloads(
                $command,
                $identities,
            ),
            parentProcess: $command->parentProcess,
        );
    }

    private function getSourcePayloads(
        StartBatchSync $command,
        array $identities,
    ): array {
        $sourcePayloads = [];

        foreach ($identities as $identity) {
            $sourceKey = $identity->key();

            if (array_key_exists(
                $sourceKey,
                $command->sourcePayloads,
            )) {
                $sourcePayloads[$sourceKey] =
                    $command->sourcePayloads[$sourceKey];
            }
        }

        return $sourcePayloads;
    }
}
