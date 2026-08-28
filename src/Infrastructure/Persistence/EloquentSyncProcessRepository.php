<?php

namespace Synchub\LaravelSynchub\Infrastructure\Persistence;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncProcessRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;
use Synchub\LaravelSynchub\Domain\Sync\Facades\Sync;
use Synchub\LaravelSynchub\Domain\Sync\Models\SyncProcess;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;

final class EloquentSyncProcessRepository implements SyncProcessRepository
{
    public function create(array $data): SyncProcessEntity
    {
        return $this->toEntity(
            SyncProcess::query()->create($data),
        );
    }

    public function update(
        SyncProcessEntity $process,
        array $data,
    ): bool {
        return SyncProcess::query()
            ->whereKey($process->id)
            ->update($data) > 0;
    }

    public function transition(
        SyncProcessEntity $process,
        SyncProcessStatus $from,
        SyncProcessStatus $to,
        array $data = [],
    ): bool {
        return SyncProcess::query()
            ->whereKey($process->id)
            ->where('status', $from->value)
            ->update([
                'status' => $to->value,
                ...$data,
            ]) > 0;
    }

    public function findOrFail(int $id): SyncProcessEntity
    {
        return $this->toEntity(
            SyncProcess::query()->findOrFail($id),
        );
    }

    public function find(int $id): ?SyncProcessEntity
    {
        $process = SyncProcess::query()->find($id);

        return $process
            ? $this->toEntity($process)
            : null;
    }

    /**
     * Deve ser chamado dentro de uma transaction.
     *
     * @return SyncProcessEntity[]
     */
    public function findActiveForUpdate(
        string $context,
        SourceIdentity $identity,
    ): array {
        return SyncProcess::query()
            ->where('context', $context)
            ->where('source_key', $identity->key())
            ->whereIn('status', [
                SyncProcessStatus::PENDING,
                SyncProcessStatus::PROCESSING,
                SyncProcessStatus::WAITING_DEPENDENCY,
            ])
            ->lockForUpdate()
            ->get()
            ->map(
                fn (SyncProcess $model): SyncProcessEntity =>
                    $this->toEntity($model)
            )
            ->all();
    }

    public function findReusableProcess(
        string $context,
        SourceIdentity $identity,
    ): ?SyncProcessEntity {
        $process = SyncProcess::query()
            ->where('context', $context)
            ->where('source_key', $identity->key())
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
        SyncProcess $model,
    ): SyncProcessEntity {
        return new SyncProcessEntity(
            id: $model->id,
            context: $model->context,

            sourceIdentity: SourceIdentity::from(
                $model->source_identity,
            ),

            status: $model->status,
            currentStep: $model->current_step,
            force: $model->force,

            sourcePayload: $model->source_payload ?? [],
            targetPayload: $model->target_payload ?? [],
            targetResponse: $model->target_response ?? [],
            error: $model->error ?? [],

            payloadCachedAt: $model->payload_cached_at,
            startedAt: $model->started_at,
            resumedAt: $model->resumed_at,
            finishedAt: $model->finished_at,
        );
    }
}
