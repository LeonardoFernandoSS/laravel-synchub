<?php

namespace Synchub\LaravelSynchub\Infrastructure\Persistence;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncDependencyRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncDependencyEntity;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;
use Synchub\LaravelSynchub\Domain\Sync\Models\SyncDependency;

class EloquentSyncDependencyRepository implements SyncDependencyRepository
{
    public function create(
        SyncProcessEntity $process,
        SyncProcessEntity $dependencyProcess,
        string $context,
        mixed $sourceId
    ): SyncDependencyEntity {

        $dependency = SyncDependency::firstOrCreate(
            [
                'sync_process_id' => $process->id,
                'context' => $context,
                'source_id' => $sourceId,
            ],
            [
                'depends_on_process_id' => $dependencyProcess->id,
                'resolved' =>
                $dependencyProcess->status === SyncProcessStatus::SUCCESS,
                'resolved_at' =>
                $dependencyProcess->status === SyncProcessStatus::SUCCESS
                    ? now()
                    : null,
            ]
        );

        return $this->toEntity($dependency);
    }


    public function findPendingByDependencyProcess(
        int $processId
    ): array {

        return SyncDependency::query()
            ->where('depends_on_process_id', $processId)
            ->where('resolved', false)
            ->get()
            ->map(fn(SyncDependency $dependency) => $this->toEntity($dependency))
            ->all();
    }


    public function resolve(
        SyncDependencyEntity $dependency
    ): void {

        SyncDependency::whereKey($dependency->id)
            ->update([
                'resolved' => true,
                'resolved_at' => now(),
            ]);

        $dependency->resolved = true;
        $dependency->resolvedAt = now();
    }


    public function existsPendingForProcess(
        int $processId
    ): bool {

        return SyncDependency::query()
            ->where('sync_process_id', $processId)
            ->where('resolved', false)
            ->exists();
    }


    private function toEntity(
        SyncDependency $model
    ): SyncDependencyEntity {

        return new SyncDependencyEntity(
            id: $model->id,
            processId: $model->sync_process_id,
            dependsOnProcessId: $model->depends_on_process_id,
            context: $model->context,
            sourceId: $model->source_id,
            resolved: $model->resolved,
            resolvedAt: $model->resolved_at,
        );
    }
}
