<?php

namespace Synchub\LaravelSynchub\Application\Sync\Handlers;

use Synchub\LaravelSynchub\Application\Sync\Commands\StartSync;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessRelationService;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;

class CreateSyncProcessHandler
{
    public function __construct(
        private SyncProcessService $processService,
        private SyncProcessRelationService $relationService,
    ) {}

    public function handle(StartSync $command): SyncProcessEntity
    {
        $process = $this->processService->create([
            'type' => $command->context . '_sync',
            'context' => $command->context,
            'context_id' => $command->id,
            'status' => SyncProcessStatus::PENDING,
            'current_step' => SyncProcessStep::CREATED,
            'force' => $command->force,
        ]);

        if ($command->parentProcess) {
            $this->relationService->link(
                parent: $command->parentProcess,
                child: $process,
                type: 'triggered',
            );
        }

        return $process;
    }
}
