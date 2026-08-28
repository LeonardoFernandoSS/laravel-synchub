<?php

namespace Synchub\LaravelSynchub\Domain\Sync\DTO;

use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\TargetIdentity;

final class SyncResultData
{
    public function __construct(
        public readonly TargetIdentity $identity,
        public readonly array $raw
    ) {}
}
