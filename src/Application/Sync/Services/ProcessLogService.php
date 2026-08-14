<?php

namespace Synchub\LaravelSynchub\Application\Sync\Services;

use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncLogRepository;
use Synchub\LaravelSynchub\Domain\Sync\Entities\SyncProcessEntity;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncLogType;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;

class ProcessLogService
{
    public function __construct(
        protected SyncLogRepository $logRepository,
    ) {}

    /**
     * Registra uma mensagem de processo.
     *
     * Mensagens conhecidas de auditoria são registradas como TIMELINE.
     * Todas as demais mensagens são DEBUG.
     *
     * DEBUG não é persistido em produção.
     */
    public function log(
        SyncProcessEntity $process,
        SyncProcessMessage|string $message,
        array $payload = [],
    ): void {
        $type = $this->resolveType($message);

        if ($type === SyncLogType::DEBUG && app()->isProduction()) {
            return;
        }

        $this->write(
            process: $process,
            message: $message,
            payload: $payload,
            type: $type,
        );
    }

    /**
     * Registra explicitamente uma mensagem de debug.
     *
     * Nunca é persistida em produção.
     */
    public function debug(
        SyncProcessEntity $process,
        SyncProcessMessage|string $message,
        array $payload = [],
    ): void {
        if (app()->isProduction()) {
            return;
        }

        $this->write(
            process: $process,
            message: $message,
            payload: $payload,
            type: SyncLogType::DEBUG,
        );
    }

    private function resolveType(
        SyncProcessMessage|string $message,
    ): SyncLogType {
        /*
         * Strings livres nunca fazem parte da auditoria.
         * Para entrarem na timeline, devem primeiro virar um
         * SyncProcessMessage explicitamente definido.
         */
        if (!$message instanceof SyncProcessMessage) {
            return SyncLogType::DEBUG;
        }

        return match ($message) {
            /*
             * =========================================================
             * HISTÓRIA / AUDITORIA
             * =========================================================
             *
             * Eventos que representam uma mudança relevante no
             * ciclo de vida ou no resultado da sincronização.
             */

            SyncProcessMessage::PROCESS_CREATED,
            SyncProcessMessage::PROCESS_STARTED,
            SyncProcessMessage::PROCESSING_STARTED,
            SyncProcessMessage::PROCESS_RESUMED,
            SyncProcessMessage::PROCESS_SUCCEEDED,
            SyncProcessMessage::PROCESS_FAILED,
            SyncProcessMessage::PROCESS_ERROR,
            SyncProcessMessage::PROCESS_OBSOLETED,

            /*
             * Dependências.
             *
             * A auditoria da dependência pertence ao processo que
             * gerou/recebeu a dependência.
             */
            SyncProcessMessage::WAITING_DEPENDENCIES,
            SyncProcessMessage::DEPENDENCY_PROCESS_CREATED,
            SyncProcessMessage::DEPENDENCY_PROCESS_REUSED,
            SyncProcessMessage::DEPENDENCY_REGISTERED,

            /*
             * Relações entre processos.
             */
            SyncProcessMessage::PROCESS_RELATION_CREATED,
            SyncProcessMessage::PROCESS_RELATION_TRIGGERED,
            SyncProcessMessage::PROCESS_RELATION_RERUN,
            SyncProcessMessage::PROCESS_RELATION_DEPENDENCY,

            /*
             * Reexecução.
             *
             * Deve aparecer na história antes da criação do novo
             * processo.
             */
            SyncProcessMessage::SYNC_RERUN_STARTED,

            /*
             * Resultado da sincronização.
             */
            SyncProcessMessage::SYNC_SKIPPED_NO_CHANGES,

            /*
             * Eventos relevantes da execução.
             */
            SyncProcessMessage::SOURCE_DATA_LOADED,
            SyncProcessMessage::DATA_VALIDATED,
            SyncProcessMessage::DATA_MAPPED,

            /*
             * Mapeamento com o destino.
             */
            SyncProcessMessage::TARGET_MAPPING_FOUND,
            SyncProcessMessage::TARGET_MAPPING_NOT_FOUND,

            /*
             * Operação realizada no destino.
             */
            SyncProcessMessage::TARGET_RECORD_CREATING,
            SyncProcessMessage::TARGET_RECORD_UPDATING,
            SyncProcessMessage::TARGET_RESPONSE_SAVED,

            /*
             * Persistência do vínculo entre origem e destino.
             */
            SyncProcessMessage::TARGET_MAPPING_SAVED,
            SyncProcessMessage::TARGET_ID_SENT_TO_SOURCE,

            /*
             * Pós-sincronização.
             */
            SyncProcessMessage::AFTER_SYNC_STARTED,
            SyncProcessMessage::PRODUCT_SYNC_EVENT_DISPATCHED,

            /*
             * Lote / pausa.
             */
            SyncProcessMessage::SYNC_PAUSED,
            SyncProcessMessage::SYNC_BATCH_DISPATCHED,

            => SyncLogType::TIMELINE,

            /*
             * =========================================================
             * DEBUG
             * =========================================================
             *
             * Mensagens operacionais/detalhes internos da execução.
             *
             * Em produção não são persistidas.
             */
            default => SyncLogType::DEBUG,
        };
    }

    private function write(
        SyncProcessEntity $process,
        SyncProcessMessage|string $message,
        array $payload,
        SyncLogType $type,
    ): void {
        $this->logRepository->create(
            $process,
            $message instanceof SyncProcessMessage
                ? $message->value
                : $message,
            $payload,
            $type,
        );
    }
}
