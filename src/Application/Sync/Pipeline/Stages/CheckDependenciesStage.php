<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;
use Synchub\LaravelSynchub\Domain\Sync\Exceptions\MissingSyncDependencyException;

class CheckDependenciesStage implements SyncStage
{
    public function __construct(
        private SyncProcessService $processService,
    ) {}

    public function handle(
        SyncExecution $execution,
        Closure $next,
    ): mixed {
        $process = $execution->process;

        $this->processService->updateStep(
            $process,
            SyncProcessStep::CHECK_DEPENDENCIES,
        );

        $dependencyChecker = $execution
            ->context
            ->dependencyChecker;

        if (!$dependencyChecker) {
            return $next($execution);
        }

        $dependencies = $dependencyChecker->check(
            $execution->sourcePayload,
        );

        if (!empty($dependencies)) {
            throw new MissingSyncDependencyException(
                $dependencies,
            );
        }

        return $next($execution);
    }
}
