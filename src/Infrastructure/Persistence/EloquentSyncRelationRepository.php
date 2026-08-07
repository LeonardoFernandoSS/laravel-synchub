<?php

namespace Synchub\LaravelSynchub\Infrastructure\Persistence;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncRelationRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Models\SyncProcessRelation;

class EloquentSyncRelationRepository implements SyncRelationRepository
{
    public function create(
        SyncProcessEntity $parent,
        SyncProcessEntity $child,
        string $type
    ): SyncProcessRelation {

        return SyncProcessRelation::firstOrCreate(
            [
                'parent_process_id' => $parent->id,
                'child_process_id' => $child->id,
                'type' => $type,
            ]
        );
    }
}
