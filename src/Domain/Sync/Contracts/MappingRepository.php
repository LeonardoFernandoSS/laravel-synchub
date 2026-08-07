<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

interface MappingRepository
{
    public function findByInternalId(
        string $mappableType,
        int $internalId
    ): ?GenericMapping;

    public function create(
        string $mappableType,
        int $internalId,
        string $externalId,
        string $externalSystem,
        string $payloadHash
    ): GenericMapping;

    public function existByInternalId(
        string $mappableType,
        int $internalId
    ): bool;

    public function getExternalIdByInternalId(
        string $mappableType,
        int $internalId
    ): ?string;
}
