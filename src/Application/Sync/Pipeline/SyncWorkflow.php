<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline;

use Illuminate\Pipeline\Pipeline;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\CheckDependenciesStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\FinishProcessStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\FindTargetMappingStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\LoadSourceDataStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\MapDataStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\SynchronizeStage;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages\ValidateSourceDataStage;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Context\SyncContext;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Exceptions\BusinessException;
use Synchub\LaravelSynchub\Domain\Sync\Exceptions\MissingSyncDependencyException;
use Throwable;

class SyncWorkflow
{
    public function __construct(
        protected SyncProcessService $process,
        protected SyncContext $context,
    ) {}

    public function resume(int $processId): void
    {
        $syncProcess = $this->process->findOrFail($processId);

        if (!$syncProcess->isRunnable()) {
            return;
        }

        try {
            $execution = new SyncExecution(
                process: $syncProcess,
                context: $this->context,
            );

            $this->process->processing($syncProcess);

            app(Pipeline::class)
                ->send($execution)
                ->through([
                    LoadSourceDataStage::class,
                    CheckDependenciesStage::class,
                    ValidateSourceDataStage::class,
                    FindTargetMappingStage::class,
                    MapDataStage::class,                    
                    SynchronizeStage::class,
                    FinishProcessStage::class,
                ])
                ->thenReturn();
        } catch (MissingSyncDependencyException $exception) {
            $this->process->waitingDependency(
                $syncProcess,
                $exception->dependencies,
            );
        } catch (BusinessException $exception) {
            $this->process->failed(
                $syncProcess,
                $exception,
            );
        } catch (Throwable $exception) {

            $this->process->log(
                $syncProcess,
                SyncProcessMessage::PROCESS_ERROR,
                [
                    'message' => $exception->getMessage(),
                ]
            );

            throw $exception;
        }
    }
}
