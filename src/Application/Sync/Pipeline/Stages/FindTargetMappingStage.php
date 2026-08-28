<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;

final class FindTargetMappingStage implements SyncStage
{
    public function __construct(
        private SyncProcessService $processService,
    ) {}

    public function handle(
        SyncExecution $execution,
        Closure $next,
    ): mixed {
        $process = $execution->process;

        $this->processService->step(
            $process,
            SyncProcessStep::FIND_TARGET_MAPPING,
            SyncProcessMessage::TARGET_MAPPING_FINDING,
        );

        $execution->mapping = $execution
            ->context
            ->repository
            ->find(
                context: $process->context,
                identity: $process->sourceIdentity,
            );

        if ($execution->mapping === null) {
            $this->processService->log(
                $process,
                SyncProcessMessage::TARGET_MAPPING_NOT_FOUND,
            );
        } else {
            $this->processService->log(
                $process,
                SyncProcessMessage::TARGET_MAPPING_FOUND,
                [
                    'target_identity' => $execution->mapping->targetIdentity->values(),
                    'payload_hash' => $execution->mapping->payloadHash,
                ],
            );
        }

        return $next($execution);
    }
}
