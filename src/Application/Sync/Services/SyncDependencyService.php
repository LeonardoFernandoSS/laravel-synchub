<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Illuminate\Support\Facades\DB;
use Synchub\LaravelSynchub\Application\Sync\Events\DependencyResolved;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncDependencyRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncDependencyEntity;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;

final class SyncDependencyService
{
    public function __construct(
        protected SyncDependencyRepository $repository,
    ) {}

    public function resolve(
        SyncProcessEntity $process,
    ): void {
        
        $dependencies = DB::transaction(
            function () use ($process): array {
                $dependencies = $this->repository
                    ->findPendingByDependencyProcess(
                        $process->id,
                    );

                foreach ($dependencies as $dependency) {
                    $this->repository->resolve(
                        $dependency,
                    );
                }

                return $dependencies;
            },
        );

        if ($dependencies === []) {
            return;
        }

        foreach ($dependencies as $dependency) {
            DependencyResolved::dispatch(
                $dependency,
            );
        }
    }

    public function addDependency(
        SyncProcessEntity $process,
        SyncProcessEntity $dependencyProcess,
        string $context,
        SourceIdentity $sourceIdentity,
    ): SyncDependencyEntity {
        return $this->repository->create(
            $process,
            $dependencyProcess,
            $context,
            $sourceIdentity,
        );
    }
}
