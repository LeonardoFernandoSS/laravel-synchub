<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncProcessRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

class ProcessFinderService
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
        mixed $sourceId,
    ): ?SyncProcessEntity {
        return $this->repository->findReusableProcess(
            $context,
            $sourceId,
        );
    }
}