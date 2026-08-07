<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Context;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\AfterSyncHandler;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\DependencyChecker;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\ContextMapper;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\ContextValidator;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\ExternalEntityGateway;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\InternalEntityGateway;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\MappingRepository;

class SyncContext
{
    public function __construct(
        public InternalEntityGateway $internal,
        public ExternalEntityGateway $external,
        public ContextMapper $mapper,
        public ContextValidator $validator,
        public MappingRepository $mapping,
        public ?DependencyChecker $dependencies = null,
        public ?AfterSyncHandler $afterSync = null,
    ) {
    }
}