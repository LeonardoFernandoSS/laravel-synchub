<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncProcessRepository;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncDependencyData;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Application\Sync\Events\MissingDependenciesDetected;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessStarted;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessSucceeded;
use Synchub\LaravelSynchub\Application\Sync\Events\WaitingDependency;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;
use Throwable;

class ProcessLifecycleService
{
    public function __construct(
        protected SyncProcessRepository $repository,
        private ProcessLogService $logService,
    ) {}

    public function create(
        array $data

    ): SyncProcessEntity {

        $process = $this->repository->create([
            ...$data,

            'status' =>
            $data['status']
                ?? SyncProcessStatus::PENDING,

            'current_step' =>
            $data['current_step']
                ?? SyncProcessStep::CREATED,

            'force' =>
            $data['force']
                ?? false,
        ]);

        $this->logService->log(
            $process,
            SyncProcessMessage::PROCESS_CREATED,
            [
                'process_id' => $process->id,
                'type' => $data['type'],
                'context' => $data['context'],
                'context_id' => $data['context_id'],
                'force' => $data['force'],
            ]
        );

        return $process;
    }

    public function start(
        string $type,
        string $context,
        int $contextId,
        bool $force = false
    ): SyncProcessEntity {

        $this->repository->obsoleteRunning(
            $type,
            $context,
            $contextId
        );

        $process = $this->repository->create([
            'type' => $type,
            'context' => $context,
            'context_id' => $contextId,
            'status' => SyncProcessStatus::PROCESSING,
            'current_step' => SyncProcessStep::START,
            'force' => $force,
            'started_at' => now(),
        ]);

        $this->logService->log(
            $process,
            SyncProcessMessage::PROCESS_STARTED,
            [
                'process_id' => $process->id,
                'type' => $type,
                'context' => $context,
                'context_id' => $contextId,
                'force' => $force,
            ]
        );

        return $process;
    }

    public function resume(
        SyncProcessEntity $process
    ): void {

        $this->repository->update(
            $process,
            [
                'status' => SyncProcessStatus::PENDING,
                'current_step' => SyncProcessStep::START,
            ]
        );

        $this->logService->log(
            $process,
            SyncProcessMessage::PROCESS_RESUMED
        );
    }

    public function processing(SyncProcessEntity $process): void
    {
        $this->repository->update($process, [
            'status' => SyncProcessStatus::PROCESSING,
            'started_at' => now(),
        ]);

        $this->logService->log(
            $process,
            'Processo iniciado',
            [
                'force' => $process->force,
                'context' => $process->context,
                'context_id' => $process->contextId,
            ]
        );

        ProcessStarted::dispatch($process);
    }

    public function success(
        SyncProcessEntity $process,
        array $response = []
    ): void {

        $this->repository->update($process, [
            'status' => SyncProcessStatus::SUCCESS,
            'current_step' => SyncProcessStep::FINISHED,
            'external_response' => $response,
            'finished_at' => now(),
        ]);

        ProcessSucceeded::dispatch($process);

        $this->logService->log(
            $process,
            SyncProcessMessage::PROCESS_SUCCESS
        );
    }

    public function failed(
        SyncProcessEntity $process,
        Throwable $e
    ): void {

        $this->repository->update($process, [
            'status' => SyncProcessStatus::FAILED,
            'error' => [
                'message' => $e->getMessage(),
                'errors' => $e->errors,
            ],
            'finished_at' => now(),
        ]);

        $this->logService->log(
            $process,
            SyncProcessMessage::PROCESS_FAILED,
            [
                'message' => $e->getMessage(),
                'errors' => $e->errors,
            ]
        );
    }

    public function error(
        SyncProcessEntity $process,
        Throwable $e
    ): void {

        $this->repository->update($process, [
            'status' => SyncProcessStatus::ERROR,
            'error' => $e->getMessage(),
            'finished_at' => now(),
        ]);

        $this->logService->log(
            $process,
            SyncProcessMessage::PROCESS_ERROR,
            [
                'error' => $e->getMessage()
            ]
        );
    }

    public function obsolete(SyncProcessEntity $process): void
    {
        $this->repository->update($process, [
            'status' => SyncProcessStatus::OBSOLETE,
            'finished_at' => now(),
        ]);

        $this->logService->log(
            $process,
            SyncProcessMessage::PROCESS_OBSOLETE,
            [
                'process_id' => $process->id,
                'context' => $process->context,
                'context_id' => $process->contextId,
            ]
        );
    }

    /**
     * @param SyncDependencyData[] $dependencies
     */
    public function waitingDependency(
        SyncProcessEntity $process,
        $dependencies,
    ): void {

        $this->repository->update($process, [
            'status' => SyncProcessStatus::WAITING_DEPENDENCY,
            'current_step' => SyncProcessStep::WAITING_DEPENDENCY,
        ]);

        MissingDependenciesDetected::dispatch($process, $dependencies);
    }

    public function updateStep(
        SyncProcessEntity $process,
        SyncProcessStep $step
    ): void {
        $this->repository->update($process, [
            'current_step' => $step,
        ]);
    }

    public function step(
        SyncProcessEntity $process,
        SyncProcessStep $step,
        SyncProcessMessage|string $message,
        array $payload = []
    ): void {

        $this->repository->update($process, [
            'current_step' => $step,
        ]);

        $this->logService->log(
            $process,
            $message,
            $payload
        );
    }

    public function isObsolete(
        SyncProcessEntity $process
    ): bool {

        return $process->status === SyncProcessStatus::OBSOLETE;
    }
}
