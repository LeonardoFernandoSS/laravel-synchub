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

    public function findReusableProcess(
        string $context,
        int $contextId
    ): ?SyncProcessEntity {

        return $this->repository->findReusableProcess(
            $context,
            $contextId
        );
    }
}
