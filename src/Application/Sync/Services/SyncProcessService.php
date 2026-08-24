<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncDependencyData;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;
use Throwable;

final class SyncProcessService
{
    public function __construct(
        private ProcessLifecycleService $lifecycle,
        private ProcessPayloadService $payload,
        private ProcessFinderService $finder,
        private ProcessLogService $logs,
    ) {}

    public function create(array $data): SyncProcessEntity
    {
        return $this->lifecycle->create($data);
    }

    public function start(
        string $context,
        mixed $sourceId,
        bool $force = false,
    ): SyncProcessEntity {
        return $this->lifecycle->start(
            $context,
            $sourceId,
            $force,
        );
    }

    public function findOrFail(int $id): SyncProcessEntity
    {
        return $this->finder->findOrFail($id);
    }

    public function find(int $id): ?SyncProcessEntity
    {
        return $this->finder->find($id);
    }

    public function findReusableProcess(
        string $type,
        string $context,
        mixed $sourceId,
    ): ?SyncProcessEntity {
        return $this->finder->findReusableProcess(
            $context,
            $sourceId,
        );
    }

    public function processing(
        SyncProcessEntity $process,
    ): void {
        $this->lifecycle->processing($process);
    }

    public function success(
        SyncProcessEntity $process,
        array $targetResponse = [],
    ): void {
        $this->lifecycle->success(
            $process,
            $targetResponse,
        );
    }

    public function failed(
        SyncProcessEntity $process,
        Throwable $exception,
    ): void {
        $this->lifecycle->failed(
            $process,
            $exception,
        );
    }

    public function error(
        SyncProcessEntity $process,
        Throwable $exception,
    ): void {
        $this->lifecycle->error(
            $process,
            $exception,
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

    public function obsolete(
        SyncProcessEntity $process,
    ): void {
        $this->lifecycle->obsolete($process);
    }

    public function updateStep(
        SyncProcessEntity $process,
        SyncProcessStep $step,
    ): void {
        $this->lifecycle->updateStep(
            $process,
            $step,
        );
    }

    public function step(
        SyncProcessEntity $process,
        SyncProcessStep $step,
        SyncProcessMessage|string $message,
        array $payload = [],
    ): void {
        $this->lifecycle->step(
            $process,
            $step,
            $message,
            $payload,
        );
    }

    public function isStillRunnable(
        SyncProcessEntity $process,
    ): bool {
        return $this->lifecycle->isStillRunnable($process);
    }


    public function log(
        SyncProcessEntity $process,
        SyncProcessMessage|string $message,
        array $payload = [],
    ): void {
        $this->logs->log(
            $process,
            $message,
            $payload,
        );
    }

    public function debug(
        SyncProcessEntity $process,
        SyncProcessMessage|string $message,
        array $payload = [],
    ): void {
        $this->logs->debug(
            $process,
            $message,
            $payload,
        );
    }

    public function getSourcePayload(
        SyncProcessEntity $process,
    ): array {
        return $this->payload->getSourcePayload(
            $process,
        );
    }

    public function saveSourcePayload(
        SyncProcessEntity $process,
        array $payload,
    ): void {
        $this->payload->saveSourcePayload(
            $process,
            $payload,
        );
    }

    public function invalidateSourcePayload(
        SyncProcessEntity $process,
    ): void{
        $this->payload->invalidateSourcePayload(
            $process,
        );
    }

    public function saveTargetPayload(
        SyncProcessEntity $process,
        array $payload,
    ): void {
        $this->payload->saveTargetPayload(
            $process,
            $payload,
        );
    }

    public function saveTargetResponse(
        SyncProcessEntity $process,
        array $response,
    ): void {
        $this->payload->saveTargetResponse(
            $process,
            $response,
        );
    }

    public function canReuseSourcePayload(
        SyncProcessEntity $process,
    ): bool {
        return $this->payload->canReuseSourcePayload($process);
    }
}
