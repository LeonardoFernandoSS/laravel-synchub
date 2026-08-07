<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

abstract class AfterSyncHandler
{
    public function __construct(
        protected SyncProcessService $syncProcess
    ) {}

    abstract public function handle(SyncProcessEntity $process): void;
}
