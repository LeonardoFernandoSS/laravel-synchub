<?php

namespace Synchub\LaravelSynchub\Application\Sync\Handlers;

use Synchub\LaravelSynchub\Application\Sync\Commands\StartSync;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessRelationService;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;

final class CreateSyncProcessHandler
{
    public function __construct(
        private SyncProcessService $processService,
        private SyncProcessRelationService $relationService,
    ) {}

    public function handle(
        StartSync $command,
    ): SyncProcessEntity {
        $process = $this->processService->start(
            context: $command->context,
            sourceId: $command->sourceId,
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

        return $process;
    }
}