<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Models\SyncProcessRelation;

interface SyncRelationRepository
{
    public function create(
        SyncProcessEntity $parent,
        SyncProcessEntity $child,
        string $type
    ): SyncProcessRelation;
}
