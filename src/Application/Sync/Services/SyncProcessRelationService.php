<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncRelationRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessRelationType;

final class SyncProcessRelationService
{
    public function __construct(
        private SyncRelationRepository $repository,
        private SyncProcessService $syncProcess,
    ) {}

    public function link(
        SyncProcessEntity $parent,
        SyncProcessEntity $child,
        SyncProcessRelationType $type,
    ): void {
        $this->repository->create(
            $parent,
            $child,
            $type,
        );

        $this->syncProcess->log(
            $child,
            $this->messageFor($type),
            [
                'relation_type' => $type->value,
                'parent_process_id' => $parent->id,
                'parent_context' => $parent->context,
                'parent_entity_id' => $parent->sourceId,
                'child_process_id' => $child->id,                
            ],
            
        );
    }

    public function messageFor(
        SyncProcessRelationType $type,
    ): SyncProcessMessage {
        return match ($type) {
            SyncProcessRelationType::TRIGGERED =>
            SyncProcessMessage::PROCESS_RELATION_TRIGGERED,

            SyncProcessRelationType::RERUN =>
            SyncProcessMessage::PROCESS_RELATION_RERUN,

            SyncProcessRelationType::DEPENDENCY =>
                SyncProcessMessage::PROCESS_RELATION_DEPENDENCY,
        };
    }
}