<?php

namespace Synchub\LaravelSynchub\Domain\Sync\DTO;

final class SyncData
{
    public readonly string $hash;

    public function __construct(
        public readonly array $payload,
    ) {
        $this->hash = hash(
            'sha256',
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            )
        );
    }
}
