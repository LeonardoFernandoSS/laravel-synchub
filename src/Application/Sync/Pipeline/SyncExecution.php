<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline;

use Synchub\LaravelSynchub\Domain\Sync\Context\SyncContext;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\GenericMapping;
use Synchub\LaravelSynchub\Domain\Sync\DTO\ExternalSyncResponse;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncData;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

class SyncExecution
{
    public function __construct(
        public SyncProcessEntity $process,
        public SyncContext $context,
    ) {}

    public array $internalPayload = [];

    public ?SyncData $mappedData = null;

    public ?GenericMapping $mapping = null;

    public ?ExternalSyncResponse $response = null;
}