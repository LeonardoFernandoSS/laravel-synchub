<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

interface InternalEntityGateway
{
    public function find(int $id): array;

    public function saveExternalId(
        int $id,
        string $externalId
    ): void;
}
