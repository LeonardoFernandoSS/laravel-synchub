<?php

namespace Synchub\LaravelSynchub\Application\Sync\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Synchub\LaravelSynchub\Application\Sync\Commands\StartSync;
use Synchub\LaravelSynchub\Application\Sync\Handlers\StartSyncHandler;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;

final class ProcessBatchSync implements ShouldQueue
{
    use Queueable;
    use Batchable;

    /**
     * @param SourceIdentity[] $identities
     */
    public function __construct(
        public string $context,
        public array $identities,
        public bool $force,
        public ?SyncProcessEntity $parentProcess = null,
    ) {}

    public function handle(
        StartSyncHandler $handler,
    ): void {
        foreach ($this->identities as $identity) {
            $handler->handle(
                new StartSync(
                    context: $this->context,
                    identity: $identity,
                    force: $this->force,
                    parentProcess: $this->parentProcess,
                ),
            );
        }
    }
}
