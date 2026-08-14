<?php

namespace Synchub\LaravelSynchub\Application\Sync\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Synchub\LaravelSynchub\Application\Sync\Commands\StartSync;
use Synchub\LaravelSynchub\Application\Sync\Handlers\StartSyncHandler;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

class ProcessBatchSync implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public function __construct(
        public string $context,
        public array $ids,
        public bool $force,
        public ?SyncProcessEntity $parentProcess = null,
    ) {}

    public function handle(
        StartSyncHandler $handler,
    ): void {
        foreach ($this->ids as $id) {
            $handler->handle(
                new StartSync(
                    context: $this->context,
                    id: $id,
                    force: $this->force,
                    parentProcess: $this->parentProcess,
                )
            );
        }
    }
}
