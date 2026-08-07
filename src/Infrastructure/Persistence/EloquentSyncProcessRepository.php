<?php

namespace Synchub\LaravelSynchub\Infrastructure\Persistence;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncProcessRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;
use Synchub\LaravelSynchub\Domain\Sync\Models\SyncProcess;

class EloquentSyncProcessRepository implements SyncProcessRepository
{
    public function create(
        array $data
    ): SyncProcessEntity {

        return $this->toEntity(
            SyncProcess::create($data)
        );
    }


    public function update(
        SyncProcessEntity $process,
        array $data
    ): bool {

        return SyncProcess::where(
            'id',
            $process->id
        )
            ->update($data) > 0;
    }


    public function findOrFail(
        int $id
    ): SyncProcessEntity {

        return $this->toEntity(
            SyncProcess::findOrFail($id)
        );
    }


    public function obsoleteRunning(
        string $type,
        string $context,
        int $contextId
    ): void {

        SyncProcess::query()
            ->where('type', $type)
            ->where('context', $context)
            ->where('context_id', $contextId)
            ->where(
                'status',
                SyncProcessStatus::PROCESSING
            )
            ->update([
                'status' => SyncProcessStatus::OBSOLETE,
                'finished_at' => now(),
            ]);
    }

    public function findReusableProcess(
        string $context,
        int $contextId
    ): ?SyncProcessEntity {

        $process = SyncProcess::query()
            ->where('context', $context)
            ->where('context_id', $contextId)
            ->whereIn('status', [
                SyncProcessStatus::SUCCESS,
                SyncProcessStatus::PROCESSING,
                SyncProcessStatus::PENDING,
            ])
            ->latest('id')
            ->first();


        return $process
            ? $this->toEntity($process)
            : null;
    }

    private function toEntity(
        SyncProcess $model
    ): SyncProcessEntity {

        return new SyncProcessEntity(

            id: $model->id,

            type: $model->type,

            context: $model->context,

            contextId: $model->context_id,

            status: $model->status,

            step: $model->current_step,

            force: $model->force,

            internalPayload: $model->internal_payload ?? [],

            mappedPayload: $model->mapped_payload ?? [],

            externalResponse: $model->external_response ?? [],

            error: $model->error ?? [],

            payloadCachedAt: $model->payload_cached_at,
        );
    }
}
