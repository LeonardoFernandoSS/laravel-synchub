<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncProcessRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;

class ProcessPayloadService
{
    public function __construct(
        protected SyncProcessRepository $repository,
        private ProcessLogService $logService,
    ) {}


    public function saveInternalPayload(
        SyncProcessEntity $process,
        array $payload
    ): void {

        $this->repository->update(
            $process,
            [
                'internal_payload' => $payload,
                'payload_cached_at' => now(),
            ]
        );


        $process->internalPayload = $payload;
        $process->payloadCachedAt = now();


        $this->logService->log(
            $process,
            SyncProcessMessage::INTERNAL_PAYLOAD_SAVED,
            $payload
        );
    }


    public function saveMappedPayload(
        SyncProcessEntity $process,
        array $payload
    ): void {

        $this->repository->update(
            $process,
            [
                'mapped_payload' => $payload,
            ]
        );


        $process->mappedPayload = $payload;


        $this->logService->log(
            $process,
            SyncProcessMessage::MAPPED_PAYLOAD_SAVED,
            $payload
        );
    }


    public function saveExternalResponse(
        SyncProcessEntity $process,
        array $response
    ): void {

        $this->repository->update(
            $process,
            [
                'external_response' => $response,
            ]
        );


        $process->externalResponse = $response;


        $this->logService->log(
            $process,
            SyncProcessMessage::EXTERNAL_RESPONSE_SAVED,
            $response
        );
    }


    public function canReuseInternalPayload(
        SyncProcessEntity $process
    ): bool {

        if (!$process->internalPayload) {
            return false;
        }

        if (!$process->payloadCachedAt) {
            return false;
        }

        return $process->payloadCachedAt->getTimestamp()
            > time() - (10 * 60);
    }

    private function hasInternalPayload(
        SyncProcessEntity $process
    ): bool {

        return !empty($process->internalPayload);
    }


    public function getInternalPayload(
        SyncProcessEntity $process
    ): array {

        return $process->internalPayload;
    }
}
