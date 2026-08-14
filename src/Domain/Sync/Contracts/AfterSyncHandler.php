<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

abstract class AfterSyncHandler
{
    abstract public function handle(SyncProcessEntity $process): void;
}
