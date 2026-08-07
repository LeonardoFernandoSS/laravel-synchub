<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncDependencyRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncDependencyEntity;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Application\Sync\Events\DependencyResolved;

class SyncDependencyService
{
    public function __construct(
        protected SyncDependencyRepository $repository
    ) {}


    public function resolve(
        SyncProcessEntity $process
    ): void {

        $dependencies =
            $this->repository
            ->findPendingByDependencyProcess(
                $process->id
            );
            
        foreach ($dependencies as $dependency) {

            $this->repository->resolve(
                $dependency
            );

            DependencyResolved::dispatch(
                $dependency
            );
        }
    }


    public function addDependency(
        SyncProcessEntity $process,
        SyncProcessEntity $dependencyProcess,
        string $context,
        int $contextId
    ): SyncDependencyEntity {

        return $this->repository->create(
            $process,
            $dependencyProcess,
            $context,
            $contextId
        );
    }
}
