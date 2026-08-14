<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncProcessRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;

class ProcessPayloadService
{
    public function __construct(
        protected SyncProcessRepository $processRepository,
        private ProcessLogService $logService,
    ) {}

    public function saveSourcePayload(
        SyncProcessEntity $process,
        array $payload,
    ): void {
        $cachedAt = now();

        $this->processRepository->update(
            $process,
            [
                'source_payload' => $payload,
                'payload_cached_at' => $cachedAt,
            ]
        );

        $process->sourcePayload = $payload;
        $process->payloadCachedAt = $cachedAt;

        $this->logService->log(
            $process,
            SyncProcessMessage::SOURCE_PAYLOAD_SAVED,
            $payload,
        );
    }

    public function saveTargetPayload(
        SyncProcessEntity $process,
        array $payload,
    ): void {
        $this->processRepository->update(
            $process,
            [
                'target_payload' => $payload,
            ]
        );

        $process->targetPayload = $payload;

        $this->logService->log(
            $process,
            SyncProcessMessage::TARGET_PAYLOAD_SAVED,
            $payload,
        );
    }

    public function saveTargetResponse(
        SyncProcessEntity $process,
        array $response,
    ): void {
        $this->processRepository->update(
            $process,
            [
                'target_response' => $response,
            ]
        );

        $process->targetResponse = $response;

        $this->logService->log(
            $process,
            SyncProcessMessage::TARGET_RESPONSE_SAVED,
            $response,
        );
    }

    public function canReuseSourcePayload(
        SyncProcessEntity $process,
    ): bool {
        if (!$this->hasSourcePayload($process)) {
            return false;
        }

        if (!$process->payloadCachedAt) {
            return false;
        }

        return $process->payloadCachedAt->getTimestamp()
            > now()->subMinutes(10)->getTimestamp();
    }

    public function hasSourcePayload(
        SyncProcessEntity $process,
    ): bool {
        return !empty($process->sourcePayload);
    }

    public function getSourcePayload(
        SyncProcessEntity $process,
    ): array {

        $this->logService->log(
            $process,
            SyncProcessMessage::SOURCE_PAYLOAD_LOADED_FROM_CACHE,
            $process->sourcePayload,
        );

        return $process->sourcePayload;
    }

    public function invalidateSourcePayload(
        SyncProcessEntity $process,
    ): void {
        $this->processRepository->update(
            $process,
            [
                'payload_cached_at' => null,
            ],
        );
        
        $process->payloadCachedAt = null;
    }
}
