<?php

namespace Synchub\LaravelSynchub\Application\Sync\Handlers;

use Synchub\LaravelSynchub\Application\Sync\Commands\StartSync;
use Synchub\LaravelSynchub\Application\Sync\Services\ProcessLogService;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessRelationService;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

final class CreateSyncProcessHandler
{
    public function __construct(
        private SyncProcessService $processService,
        private SyncProcessRelationService $relationService,
        private ProcessLogService $logService,
    ) {}

    public function handle(
        StartSync $command,
    ): SyncProcessEntity {
        $process = $this->processService->start(
            context: $command->context,
            entityId: $command->id,
            force: $command->force,
        );

        if (!$command->parentProcess) {
            return $process;
        }

        $relationType = $command->relationType;

        $this->relationService->link(
            parent: $command->parentProcess,
            child: $process,
            type: $relationType,
        );

        // $this->logService->log(
        //     $process,
        //     $this->relationService->messageFor($relationType),
        //     [
        //         'parent_process_id' => $command->parentProcess->id,
        //         'parent_context' => $command->parentProcess->context,
        //         'parent_entity_id' => $command->parentProcess->entityId,
        //         'relation_type' => $relationType->value,
        //     ],
        // );

        return $process;
    }
}