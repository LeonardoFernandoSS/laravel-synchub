<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use DomainException;
use Illuminate\Support\Facades\DB;
use Synchub\LaravelSynchub\Application\Sync\Events\MissingDependenciesDetected;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessStarted;
use Synchub\LaravelSynchub\Application\Sync\Events\ProcessSucceeded;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncProcessRepository;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncDependencyData;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;
use Throwable;

final class ProcessLifecycleService
{
    public function __construct(
        protected SyncProcessRepository $processRepository,
        private ProcessLogService $logService,
    ) {}

    public function create(array $data): SyncProcessEntity
    {
        $process = $this->processRepository->create([
            ...$data,
            'status' => SyncProcessStatus::PENDING,
            'current_step' => $data['current_step']
                ?? SyncProcessStep::CREATED,
            'force' => $data['force'] ?? false,
        ]);

        $this->logService->log(
            $process,
            SyncProcessMessage::PROCESS_CREATED,
            [
                'sync_process_id' => $process->id,
                'context' => $process->context,
                'source_identity' => $process->sourceIdentity->values(),
                'force' => $process->force,
            ],
        );

        return $process;
    }

    /**
     * Cria e inicia uma nova sincronização.
     *
     * Processo anterior ativo:
     *
     * PENDING/PROCESSING/WAITING_DEPENDENCY
     *                  ↓
     *              OBSOLETE
     *
     * Novo:
     *
     * PENDING
     */
    public function start(
        string $context,
        SourceIdentity $sourceIdentity,
        bool $force = false,
        ?array $sourcePayload = null,
    ): SyncProcessEntity {
        return DB::transaction(function () use (
            $context,
            $sourceIdentity,
            $force,
            $sourcePayload,
        ): SyncProcessEntity {
            $activeProcesses = $this->processRepository
                ->findActiveForUpdate(
                    context: $context,
                    identity: $sourceIdentity,
                );

            foreach ($activeProcesses as $activeProcess) {
                $this->obsolete(
                    $activeProcess,
                    [
                        'reason' => 'replaced_by_new_process',
                    ],
                );
            }

            return $this->create([
                'context' => $context,
                'source_identity' => $sourceIdentity->values(),
                'source_key' => $sourceIdentity->key(),
                'current_step' => SyncProcessStep::CREATED,
                'force' => $force,
                'source_payload' => $sourcePayload ?? [],
                'payload_cached_at' => $sourcePayload !== null
                    ? now()
                    : null,
            ]);
        });
    }

    public function rerun(
        SyncProcessEntity $process,
    ): SyncProcessEntity {
        return DB::transaction(function () use ($process) {

            $activeProcesses = $this->processRepository
                ->findActiveForUpdate(
                    context: $process->context,
                    identity: $process->sourceIdentity,
                );

            foreach ($activeProcesses as $activeProcess) {
                if ($activeProcess->id === $process->id) {
                    continue;
                }

                $this->obsolete(
                    $activeProcess,
                    [
                        'reason' => 'rerun',
                        'replaced_process_id' => $process->id,
                    ],
                );
            }

            $this->transition(
                $process,
                SyncProcessStatus::PENDING,
                [
                    'current_step' => SyncProcessStep::CREATED,
                    'target_response' => [],
                    'error' => [],
                    'started_at' => null,
                    'resumed_at' => null,
                    'finished_at' => null,
                ],
            );

            return $process;
        });
    }

    /**
     * PENDING → PROCESSING
     *
     * WAITING_DEPENDENCY → PROCESSING
     */
    public function processing(
        SyncProcessEntity $process,
    ): void {
        $from = $process->status;

        $message = match ($from) {
            SyncProcessStatus::PENDING =>
            SyncProcessMessage::PROCESSING_STARTED,

            SyncProcessStatus::WAITING_DEPENDENCY =>
            SyncProcessMessage::PROCESS_RESUMED,

            SyncProcessStatus::PROCESSING =>
            SyncProcessMessage::PROCESSING_RESTARTED,

            default => throw new DomainException(
                sprintf(
                    'Process [%s] cannot start processing from status [%s].',
                    $process->id,
                    $from->value,
                )
            ),
        };

        $data = [];

        if (
            $from === SyncProcessStatus::PENDING
            && $process->startedAt === null
        ) {
            $data['started_at'] = now();
        }

        if ($from === SyncProcessStatus::WAITING_DEPENDENCY) {
            $data['resumed_at'] = now();
        }

        if ($process->status != SyncProcessStatus::PROCESSING) {
            $this->transition(
                $process,
                SyncProcessStatus::PROCESSING,
                $data,
            );
        }

        $this->logService->log(
            $process,
            $message,
            [
                'sync_process_id' => $process->id,
                'context' => $process->context,
                'source_identity' => $process->sourceIdentity->values(),
                'force' => $process->force,
            ],
        );

        ProcessStarted::dispatch($process);
    }

    /**
     * PROCESSING → SUCCESS
     */
    public function success(
        SyncProcessEntity $process,
        array $targetResponse = [],
    ): void {
        $this->transition(
            $process,
            SyncProcessStatus::SUCCESS,
            [
                'current_step' => SyncProcessStep::FINISHED,
                'target_response' => $targetResponse,
                'finished_at' => now(),
            ],
        );

        $this->logService->log(
            $process,
            SyncProcessMessage::PROCESS_SUCCEEDED,
            [
                'target_response' => $targetResponse,
            ],
        );

        ProcessSucceeded::dispatch($process);
    }

    /**
     * PROCESSING → FAILED
     */
    public function failed(
        SyncProcessEntity $process,
        Throwable $exception,
    ): void {
        $error = [
            'message' => $exception->getMessage(),
            'errors' => $this->extractExceptionErrors($exception),
        ];

        $this->transition(
            $process,
            SyncProcessStatus::FAILED,
            [
                'error' => $error,
                'finished_at' => now(),
            ],
        );

        $this->logService->log(
            $process,
            SyncProcessMessage::PROCESS_FAILED,
            $error,
        );
    }

    /**
     * PROCESSING → ERROR
     */
    public function error(
        SyncProcessEntity $process,
        Throwable $exception,
    ): void {
        $error = [
            'message' => $exception->getMessage(),
        ];

        $this->transition(
            $process,
            SyncProcessStatus::ERROR,
            [
                'error' => $error,
                'finished_at' => now(),
            ],
        );
    }

    /**
     * PENDING/PROCESSING/WAITING_DEPENDENCY → OBSOLETE
     */
    public function obsolete(
        SyncProcessEntity $process,
        array $metadata = [],
    ): void {
        $this->transition(
            $process,
            SyncProcessStatus::OBSOLETE,
            [
                'current_step' => SyncProcessStep::OBSOLETE,
                'finished_at' => now(),
            ],
        );

        $this->logService->log(
            $process,
            SyncProcessMessage::PROCESS_OBSOLETED,
            [
                'sync_process_id' => $process->id,
                'context' => $process->context,
                'source_identity' => $process->sourceIdentity->values(),
                ...$metadata,
            ],
        );
    }

    /**
     * PROCESSING → WAITING_DEPENDENCY
     *
     * @param SyncDependencyData[] $dependencies
     */
    public function waitingDependency(
        SyncProcessEntity $process,
        array $dependencies,
    ): void {
        $this->transition(
            $process,
            SyncProcessStatus::WAITING_DEPENDENCY,
            [
                'current_step' => SyncProcessStep::WAITING_DEPENDENCY,
            ],
        );

        $this->logService->log(
            $process,
            SyncProcessMessage::WAITING_DEPENDENCIES,
            [
                'dependencies' => array_map(fn($dependency) => $dependency->toArray(), $dependencies),
            ],
        );

        MissingDependenciesDetected::dispatch(
            $process,
            $dependencies,
        );
    }

    public function updateStep(
        SyncProcessEntity $process,
        SyncProcessStep $step,
    ): void {
        $this->setStep($process, $step);
    }

    public function step(
        SyncProcessEntity $process,
        SyncProcessStep $step,
        SyncProcessMessage|string $message,
        array $payload = [],
    ): void {
        $this->setStep($process, $step);

        $this->logService->log(
            $process,
            $message,
            $payload,
        );
    }

    private function transition(
        SyncProcessEntity $process,
        SyncProcessStatus $to,
        array $data = [],
    ): void {
        $from = $process->status;

        if ($from === $to) {
            dump($from, $to);
            throw new DomainException(
                sprintf(
                    'Process [%s] is already in status [%s].',
                    $process->id,
                    $to->value,
                )
            );
        }

        if (!$from->canTransitionTo($to)) {
            throw new DomainException(
                sprintf(
                    'Invalid transition [%s] → [%s] for process [%s].',
                    $from->value,
                    $to->value,
                    $process->id,
                )
            );
        }

        $updated = $this->processRepository->transition(
            $process,
            $from,
            $to,
            $data,
        );

        if (!$updated) {
            throw new DomainException(
                sprintf(
                    'Could not update process [%s].',
                    $process->id,
                )
            );
        }

        $process->status = $to;

        if (array_key_exists('current_step', $data)) {
            $process->currentStep = $data['current_step'];
        }

        if (array_key_exists('target_response', $data)) {
            $process->targetResponse = $data['target_response'];
        }

        if (array_key_exists('error', $data)) {
            $process->error = $data['error'];
        }

        if (array_key_exists('started_at', $data)) {
            $process->startedAt = $data['started_at'];
        }

        if (array_key_exists('resumed_at', $data)) {
            $process->resumedAt = $data['resumed_at'];
        }

        if (array_key_exists('finished_at', $data)) {
            $process->finishedAt = $data['finished_at'];
        }
    }

    private function setStep(
        SyncProcessEntity $process,
        SyncProcessStep $step,
    ): void {
        $updated = $this->processRepository->update(
            $process,
            [
                'current_step' => $step,
            ],
        );

        if (!$updated) {
            throw new DomainException(
                sprintf(
                    'Could not update step for process [%s].',
                    $process->id,
                )
            );
        }

        $process->currentStep = $step;
    }

    private function extractExceptionErrors(
        Throwable $exception,
    ): array {
        if (method_exists($exception, 'errors')) {
            $errors = $exception->errors();

            return is_array($errors) ? $errors : [];
        }

        if (property_exists($exception, 'errors')) {
            return is_array($exception->errors)
                ? $exception->errors
                : [];
        }

        return [];
    }

    public function isStillRunnable(
        SyncProcessEntity $process,
    ): bool {
        $current = $this->processRepository->find($process->id);

        return $current?->status === $process->status
            && $current->isRunnable();
    }
}
