<?php

namespace Synchub\LaravelSynchub\Infrastructure\Persistence;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\GenericMapping;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\MappingRepository;
use Synchub\LaravelSynchub\Domain\Sync\Models\Mapping;

class EloquentMappingRepository implements MappingRepository
{
    public function findByInternalId(
        string $mappableType,
        int $internalId
    ): ?GenericMapping {

        return Mapping::query()
            ->where('mappable_type', $mappableType)
            ->where('mappable_id', $internalId)
            ->first();
    }

    public function create(
        string $mappableType,
        int $internalId,
        string $externalId,
        string $externalSystem,
        string $payloadHash
    ): GenericMapping {

        return Mapping::create([
            'mappable_type' => $mappableType,
            'mappable_id' => $internalId,
            'external_id' => $externalId,
            'external_system' => $externalSystem,
            'payload_hash' => $payloadHash,
        ]);
    }

    public function existByInternalId(
        string $mappableType,
        int $internalId
    ): bool {

        return Mapping::where('mappable_type', $mappableType)
            ->where('mappable_id', $internalId)
            ->exists();
    }

    public function getExternalIdByInternalId(
        string $mappableType,
        int $internalId
    ): ?string {

        return Mapping::query()
            ->where('mappable_type', $mappableType)
            ->where('mappable_id', $internalId)
            ->value('external_id');
    }
}
