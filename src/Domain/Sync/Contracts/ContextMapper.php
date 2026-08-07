<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncData;

abstract class ContextMapper
{
    public abstract function map(array $internalPayload): SyncData;

    public function hash(array $payload): string
    {
        ksort($payload);

        return hash(
            'sha256',
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            )
        );
    }
}
