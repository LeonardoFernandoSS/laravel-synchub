<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\TargetIdentity;

interface SourceGateway
{
    public function find(
        SourceIdentity $identity,
    ): ?array;

    public function saveTargetId(
        SourceIdentity $identity,
        TargetIdentity $targetIdentity,
    ): void;
}
