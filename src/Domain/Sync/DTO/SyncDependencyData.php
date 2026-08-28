<?php

namespace Synchub\LaravelSynchub\Domain\Sync\DTO;

use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;

final class SyncDependencyData
{
    public function __construct(
        public readonly string $context,
        public readonly SourceIdentity $identity
    ) {}

    public function toArray(){
        return [
            "context" => $this->context,
            "identity" => $this->identity->values(),
        ];
    }
}
