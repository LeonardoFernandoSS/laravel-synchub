<?php

namespace Synchub\LaravelSynchub\Infrastructure\Persistence;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\MappingRepository;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncData;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncResultData;
use Synchub\LaravelSynchub\Domain\Sync\Entities\MappingEntity;
use Synchub\LaravelSynchub\Domain\Sync\Models\SyncMapping;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\TargetIdentity;

final class EloquentMappingRepository implements MappingRepository
{
    public function find(
        string $context,
        SourceIdentity $identity,
    ): ?MappingEntity {
        $mapping = SyncMapping::query()
            ->where('source_type', $context)
            ->where('source_key', $identity->key())
            ->first();

        return $mapping
            ? $this->toEntity($mapping)
            : null;
    }

    public function create(
        string $context,
        SourceIdentity $identity,
        SyncResultData $response,
        SyncData $mappedData,
    ): MappingEntity {
        $mapping = SyncMapping::create([
            'source_type' => $context,
            'source_key' => $identity->key(),
            'source_identity' => $identity->values(),
            'target_key' => $response->identity->key(),
            'target_identity' => $response->identity,
            'payload_hash' => $mappedData->hash,
            'last_payload' => $mappedData->payload
        ]);

        return $this->toEntity($mapping);
    }

    public function update(
        MappingEntity $mapping,
        SyncResultData $response,
        SyncData $mappedData,
    ): MappingEntity {
        $model = SyncMapping::query()
            ->where('source_type', $mapping->context)
            ->where(
                'source_key',
                $mapping->sourceIdentity->key(),
            )
            ->firstOrFail();

        $model->update([
            'target_identity' => $response->identity,
            'payload_hash' => $mappedData->hash,
            'last_payload' => $mappedData->payload,
        ]);

        return $this->toEntity($model->fresh());
    }

    private function toEntity(
        SyncMapping $model,
    ): MappingEntity {
        return new MappingEntity(
            context: $model->source_type,
            sourceIdentity: SourceIdentity::from(
                $model->source_identity,
            ),
            targetIdentity: $model->target_identity
                ? TargetIdentity::from($model->target_identity)
                : null,
            payloadHash: $model->payload_hash,
            lastPayload: $model->last_payload,
        );
    }
}
