<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncRelationRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;

class SyncProcessRelationService
{
    public function __construct(
        protected SyncRelationRepository $repository,
        protected SyncProcessService $syncProcess,
    ) {}


    public function link(
        SyncProcessEntity $parent,
        SyncProcessEntity $child,
        string $type,
    ): void {


        $this->repository->create(
            $parent,
            $child,
            $type
        );


        $message = match ($type) {

            'triggered' =>
            SyncProcessMessage::PROCESS_RELATION_TRIGGERED,

            'rerun' =>
            SyncProcessMessage::PROCESS_RELATION_RERUN,

            'dependency' =>
            SyncProcessMessage::PROCESS_RELATION_DEPENDENCY,

            default =>
            SyncProcessMessage::PROCESS_RELATION_CREATED,
        };


        $payload = [
            'relation_type' => $type,
            'parent_process_id' => $parent->id,
            'child_process_id' => $child->id,
        ];

        $this->syncProcess->log(
            $child,
            $message,
            $payload
        );
    }
}
