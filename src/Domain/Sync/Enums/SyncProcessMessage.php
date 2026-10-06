<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Enums;

enum SyncProcessMessage: string
{
    // Execução

    case SYNC_PROCESSING = 'Processando a sincronização.';

    case SYNC_PAUSED = 'Sincronização pausada devido a dependências pendentes.';

    // Reexecução

    case SYNC_RERUN_STARTED = 'Reexecução da sincronização iniciada.';

    // Lote

    case SYNC_BATCH_DISPATCHED = 'Lote de sincronização enviado para processamento.';

    // Dependências

    case DEPENDENCY_PROCESS_CREATED = 'Processo de dependência criado.';

    case DEPENDENCY_PROCESS_REUSED = 'Processo de dependência reutilizado.';

    case DEPENDENCY_REGISTERED = 'Dependência registrada.';

    // Relacionamentos entre processos

    case PROCESS_RELATION_CREATED = 'Relacionamento entre processos criado.';

    case PROCESS_RELATION_TRIGGERED = 'Processo originado por outro processo.';

    case PROCESS_RELATION_RERUN = 'Processo originado por reexecução.';

    case PROCESS_RELATION_DEPENDENCY = 'Processo relacionado como dependência.';

    // Processo

    case PROCESS_CREATED = 'Processo criado.';

    case PROCESS_STARTED = 'Processo iniciado.';

    case PROCESSING_STARTED = 'Processamento iniciado.';

    case PROCESSING_RESTARTED = 'Re-Processamento iniciado.';

    case PROCESS_RESUMED = 'Processo retomado após resolução das dependências.';

    case PROCESS_SUCCEEDED = 'Sincronização finalizada com sucesso.';

    case PROCESS_FAILED = 'Falha de negócio na sincronização.';

    case PROCESS_ERROR = 'Erro inesperado na sincronização.';

    case PROCESS_OBSOLETED = 'Processo marcado como obsoleto.';

    // Dependências

    case WAITING_DEPENDENCIES = 'Dependências pendentes detectadas.';

    // Carregamento de dados

    case LOADING_SOURCE_DATA = 'Carregando dados da origem.';

    case SOURCE_DATA_PROVIDED = 'Dados fornecidos na solicitação.';

    case SOURCE_DATA_LOADED = 'Dados carregados da origem.';

    case SOURCE_PAYLOAD_LOADED_FROM_CACHE = 'Payload da origem recuperado do cache.';

    case SOURCE_PAYLOAD_SAVED = 'Payload da origem salvo.';

    // Validação

    case DATA_VALIDATING = 'Validando dados.';

    case DATA_VALIDATED = 'Dados validados.';

    // Mapeamento

    case DATA_MAPPING = 'Mapeando dados.';

    case DATA_MAPPED = 'Dados mapeados.';

    case TARGET_PAYLOAD_SAVED = 'Payload do destino salvo.';

    // Busca de mapeamento

    case TARGET_MAPPING_FINDING = 'Buscando vínculo no destino.';

    case TARGET_MAPPING_FOUND = 'Vínculo no destino encontrado.';

    case TARGET_MAPPING_NOT_FOUND = 'Vínculo no destino não encontrado.';

    // Sincronização

    case TARGET_RECORD_CREATING = 'Criando registro no destino.';

    case TARGET_RECORD_UPDATING = 'Atualizando registro no destino.';

    case TARGET_RESPONSE_SAVED = 'Resposta do destino salva.';

    // Persistência do mapeamento

    case TARGET_MAPPING_SAVED = 'Vínculo com o destino salvo.';

    case TARGET_ID_SENT_TO_SOURCE = 'ID do destino enviado para a origem.';

    // Otimizações

    case SYNC_SKIPPED_NO_CHANGES = 'Sincronização ignorada. Dados sem alteração.';

    // Pós-sincronização

    case AFTER_SYNC_STARTED = 'Executando ações pós-sincronização.';

    // Eventos pós-sincronização

    case PRODUCT_SYNC_EVENT_DISPATCHED = 'Evento de produto sincronizado disparado.';
}