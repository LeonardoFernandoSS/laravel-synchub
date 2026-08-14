<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline;

use Synchub\LaravelSynchub\Domain\Sync\Context\SyncContext;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncData;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncResultData;
use Synchub\LaravelSynchub\Domain\Sync\Entities\MappingEntity;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

class SyncExecution
{
    public function __construct(
        public SyncProcessEntity $process,
        public SyncContext $context,
    ) {}

    public array $sourcePayload = [];

    public ?SyncData $targetData = null;

    public ?MappingEntity $mapping = null;

    public ?SyncResultData $targetResponse = null;
}