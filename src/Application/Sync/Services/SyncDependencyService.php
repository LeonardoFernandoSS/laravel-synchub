<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Illuminate\Support\Facades\DB;
use Synchub\LaravelSynchub\Application\Sync\Events\DependencyResolved;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessDependenciesResolved;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncDependencyRepository;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncProcessRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncDependencyEntity;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

final class SyncDependencyService
{
    public function __construct(
        protected SyncDependencyRepository $repository,
        private SyncProcessRepository $processRepository,
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

        $waitingProcessIds = array_unique(
            array_map(
                static fn(
                    SyncDependencyEntity $dependency
                ): int => $dependency->processId,
                $dependencies,
            ),
        );

        foreach ($waitingProcessIds as $waitingProcessId) {
            if (
                $this->repository->existsPendingForProcess(
                    $waitingProcessId,
                )
            ) {
                continue;
            }

            $waitingProcess = $this->processRepository->findOrFail(
                $waitingProcessId,
            );

            ProcessDependenciesResolved::dispatch(
                $waitingProcess,
            );
        }
    }

    public function addDependency(
        SyncProcessEntity $process,
        SyncProcessEntity $dependencyProcess,
        string $context,
        int $entityId,
    ): SyncDependencyEntity {
        return $this->repository->create(
            $process,
            $dependencyProcess,
            $context,
            $entityId,
        );
    }
}
