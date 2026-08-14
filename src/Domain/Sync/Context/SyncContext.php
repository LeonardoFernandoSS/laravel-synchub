<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Context;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\AfterSyncHandler;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncDependencyChecker;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncMapper;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncValidator;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\TargetGateway;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SourceGateway;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\MappingRepository;

class SyncContext
{
    public function __construct(
        public SourceGateway $source,
        public TargetGateway $target,
        public MappingRepository $repository,
        public ?SyncMapper $mapper = null,
        public ?SyncValidator $validator = null,
        public ?SyncDependencyChecker $dependencyChecker = null,
        public ?AfterSyncHandler $afterSync = null,
    ) {
    }
}