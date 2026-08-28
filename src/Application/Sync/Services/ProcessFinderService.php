<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncProcessRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;

final class ProcessFinderService
{
    public function __construct(
        protected SyncProcessRepository $repository,
    ) {}

    public function findOrFail(int $id): SyncProcessEntity
    {
        return $this->repository->findOrFail($id);
    }

    public function find(int $id): ?SyncProcessEntity
    {
        return $this->repository->find($id);
    }

    public function findReusableProcess(
        string $context,
        SourceIdentity $identity,
    ): ?SyncProcessEntity {
        return $this->repository->findReusableProcess(
            context: $context,
            identity: $identity,
        );
    }
}
