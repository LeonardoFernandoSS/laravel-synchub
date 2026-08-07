<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline;

use Throwable;
use Illuminate\Pipeline\Pipeline;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\CheckDependenciesStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\FindMappingStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\FinishProcessStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\LoadInternalDataStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\MapDataStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\SynchronizeStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\ValidateDataStage;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Context\SyncContext;
use Synchub\LaravelSynchub\Domain\Sync\Exceptions\BusinessException;
use Synchub\LaravelSynchub\Domain\Sync\Exceptions\MissingSyncDependencyException;

class SyncWorkflow
{
    public function __construct(
        protected SyncProcessService $syncProcess,
        protected SyncContext $context,
    ) {}

    public function resume(
        int $processId
    ): void {

        $process = $this->syncProcess->findOrFail($processId);

        if ($this->syncProcess->isObsolete($process)) {
            return;
        }

        try {

            $execution = new SyncExecution(
                process: $process,
                context: $this->context,
            );

            app(Pipeline::class)
                ->send($execution)
                ->through([
                    LoadInternalDataStage::class,
                    ValidateDataStage::class,
                    CheckDependenciesStage::class,
                    MapDataStage::class,
                    FindMappingStage::class,
                    SynchronizeStage::class,
                    FinishProcessStage::class,
                ])
                ->thenReturn();
        } catch (MissingSyncDependencyException $e) {

            $this->syncProcess->waitingDependency(
                $process,
                $e->dependencies
            );

            return;
        } catch (BusinessException $e) {

            $this->syncProcess->failed(
                $process,
                $e
            );
        } catch (Throwable $e) {

            $this->syncProcess->error(
                $process,
                $e
            );

            throw $e;
        }
    }
}
