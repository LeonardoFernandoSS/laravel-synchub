<?php

namespace Synchub\LaravelSynchub\Infrastructure\Persistence;

use Synchub\LaravelSynchub\Domain\Sync\Entities\MappingEntity;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\MappingRepository;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncResultData;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncData;
use Synchub\LaravelSynchub\Domain\Sync\Models\SyncMapping;

class EloquentMappingRepository implements MappingRepository
{
    public function findBySourceId(
        string $sourceType,
        int $sourceId,
    ): ?MappingEntity {

        $mapping = SyncMapping::query()
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->first();

        return $mapping
            ? $this->toEntity($mapping)
            : null;
    }

    public function create(
        string $sourceType,
        int $sourceId,
        SyncResultData $response,
        SyncData $mappedData,
    ): MappingEntity {
        $mapping = SyncMapping::create([
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'target_id' => $response->id,
            'payload_hash' => $mappedData->hash,
        ]);

        return $this->toEntity($mapping);
    }

    public function update(
        MappingEntity $mapping,
        SyncResultData $response,
        SyncData $mappedData,
    ): MappingEntity {

        $mapping = SyncMapping::query()
            ->where('source_type', $mapping->context)
            ->where('source_id', $mapping->entityId)
            ->firstOrFail();

        $mapping->update([
            'target_id' => $response->id,
            'payload_hash' => $mappedData->hash,
        ]);

        return $this->toEntity($mapping->fresh());
    }

    public function existByInternalId(
        string $mappableType,
        int $sourceId
    ): bool {

        return SyncMapping::query()
            ->where('source_type', $mappableType)
            ->where('source_id', $sourceId)
            ->exists();
    }

    public function getExternalIdByInternalId(
        string $mappableType,
        int $sourceId
    ): ?string {

        return SyncMapping::query()
            ->where('source_type', $mappableType)
            ->where('source_id', $sourceId)
            ->value('target_id');
    }

    private function toEntity(SyncMapping $model): MappingEntity
    {
        return new MappingEntity(
            context: $model->source_type,
            entityId: $model->source_id,
            targetId: $model->target_id,
            payloadHash: $model->payload_hash,
        );
    }
}
