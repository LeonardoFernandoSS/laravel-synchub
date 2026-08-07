<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncDependencyData;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncLogType;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;
use Throwable;

class SyncProcessService
{
    public function __construct(
        private ProcessLifecycleService $lifecycle,
        private ProcessPayloadService $payload,
        private ProcessFinderService $finder,
        private ProcessLogService $logs,
    ) {}

    public function create(
        array $data
    ): SyncProcessEntity {

        return $this->lifecycle->create(
            $data
        );
    }

    public function start(
        string $type,
        string $context,
        int $contextId,
        bool $force = false
    ): SyncProcessEntity {
        return $this->lifecycle->start(
            $type,
            $context,
            $contextId,
            $force
        );
    }

    public function findOrFail(int $id): SyncProcessEntity
    {
        return $this->finder->findOrFail($id);
    }

    public function resume(
        SyncProcessEntity $process
    ): void {
        $this->lifecycle->resume($process);
    }

    public function processing(SyncProcessEntity $process): void
    {
        $this->lifecycle->processing($process);
    }

    public function success(
        SyncProcessEntity $process,
        array $response = []
    ): void {
        $this->lifecycle->success(
            $process,
            $response
        );
    }

    public function failed(
        SyncProcessEntity $process,
        Throwable $e
    ): void {
        $this->lifecycle->failed(
            $process,
            $e
        );
    }

    public function error(
        SyncProcessEntity $process,
        Throwable $e
    ): void {
        $this->lifecycle->error(
            $process,
            $e
        );
    }

    public function findReusableProcess(
        string $context,
        int $contextId
    ): ?SyncProcessEntity {
        return $this->finder->findReusableProcess(
            $context,
            $contextId
        );
    }

    /**
     * @param SyncDependencyData[] $dependencies
     */
    public function waitingDependency(
        SyncProcessEntity $process,
        array $dependencies,
    ): void {
        $this->lifecycle->waitingDependency(
            $process,
            $dependencies,
        );
    }

    public function obsolete(SyncProcessEntity $process): void
    {
        $this->lifecycle->obsolete($process);
    }

    public function updateStep(
        SyncProcessEntity $process,
        SyncProcessStep $step
    ): void {
        $this->lifecycle->updateStep(
            $process,
            $step
        );
    }

    public function step(
        SyncProcessEntity $process,
        SyncProcessStep $step,
        SyncProcessMessage|string $message,
        array $payload = []
    ): void {
        $this->lifecycle->step(
            $process,
            $step,
            $message,
            $payload
        );
    }

    public function log(
        SyncProcessEntity $process,
        SyncProcessMessage|string $message,
        array $payload = [],
        SyncLogType $type = SyncLogType::TIMELINE,
    ): void {
        $this->logs->log(
            $process,
            $message,
            $payload,
            $type
        );
    }

    public function debug(
        SyncProcessEntity $process,
        SyncProcessMessage|string $message,
        array $payload = []
    ): void {
        $this->logs->debug(
            $process,
            $message,
            $payload,
        );
    }

    public function saveInternalPayload(
        SyncProcessEntity $process,
        array $payload
    ): void {
        $this->payload->saveInternalPayload(
            $process,
            $payload
        );
    }

    public function saveMappedPayload(
        SyncProcessEntity $process,
        array $payload
    ): void {
        $this->payload->saveMappedPayload(
            $process,
            $payload
        );
    }

    public function saveExternalResponse(
        SyncProcessEntity $process,
        array $response
    ): void {
        $this->payload->saveExternalResponse(
            $process,
            $response
        );
    }

    public function canReuseInternalPayload(
        SyncProcessEntity $process
    ): bool {
        return $this->payload->canReuseInternalPayload($process);
    }

    public function getInternalPayload(
        SyncProcessEntity $process
    ): array {
        return $this->payload->getInternalPayload($process);
    }

    public function isObsolete(
        SyncProcessEntity $process
    ): bool {
        return $process->status == SyncProcessStatus::OBSOLETE;
    }
}
