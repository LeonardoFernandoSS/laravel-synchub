<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Enums;

enum SyncProcessMessage: string
{
    // Execução

    case PROCESSING_SYNC = 'Processando a sincronização.';

    case SYNC_PAUSED_DEPENDENCIES = 'Sincronização pausada devido a dependências pendentes.';


        // Reexecução

    case RERUN_STARTED = 'Reexecução da sincronização iniciada.';


        // Lote

    case BATCH_DISPATCHED = 'Lote de sincronização enviado para processamento.';


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

        // Processo

    case PROCESS_CREATED = 'Processo criado.';

    case PROCESS_STARTED = 'Processo iniciado.';

    case PROCESSING_STARTED = 'Processamento iniciado.';

    case PROCESS_RESUMED = 'Processo retomado após resolução das dependências.';

    case PROCESS_SUCCESS = 'Sincronização finalizada com sucesso.';

    case PROCESS_FAILED = 'Falha de negócio na sincronização.';

    case PROCESS_ERROR = 'Erro inesperado na sincronização.';

    case PROCESS_OBSOLETE = 'Processo marcado como obsoleto.';


        // Dependências

    case WAITING_DEPENDENCIES = 'Dependências pendentes detectadas.';


        // Carregamento de dados

    case LOADING_DATA = 'Carregando dados.';

    case INTERNAL_DATA_LOADED = 'Dados carregados da API interna.';

    case INTERNAL_PAYLOAD_LOADED_FROM_CACHE = 'Payload interno recuperado do cache.';

    case INTERNAL_PAYLOAD_SAVED = 'Payload interno salvo.';


        // Validação

    case VALIDATING_DATA = 'Validando dados.';

    case DATA_VALIDATED = 'Dados validados.';


        // Mapeamento

    case MAPPING_DATA = 'Mapeando dados.';

    case DATA_MAPPED = 'Dados mapeados.';

    case MAPPED_PAYLOAD_SAVED = 'Payload mapeado salvo.';


        // Busca de vínculo externo

    case FINDING_EXTERNAL_MAPPING = 'Buscando vínculo externo.';

    case EXTERNAL_MAPPING_FOUND = 'Vínculo externo encontrado.';

    case EXTERNAL_MAPPING_NOT_FOUND = 'Vínculo externo não encontrado.';


        // Sincronização externa

    case CREATING_EXTERNAL_RECORD = 'Criando registro no sistema externo.';

    case UPDATING_EXTERNAL_RECORD = 'Atualizando registro no sistema externo.';

    case EXTERNAL_RESPONSE_SAVED = 'Resposta externa salva.';


        // Persistência de relacionamento

    case EXTERNAL_MAPPING_SAVED = 'Vínculo salvo.';

    case EXTERNAL_ID_SENT_TO_INTERNAL_API = 'ID externo enviado para API interna.';


        // Otimizações

    case SYNC_SKIPPED_NO_CHANGES = 'Sincronização ignorada. Dados sem alteração.';


        // Pós sincronização

    case AFTER_SYNC_STARTED = 'Executando ações pós sincronização.';

        // Eventos pós sincronização

    case PRODUCT_SYNC_EVENT_DISPATCHED = 'Evento de produto sincronizado disparado.';
}
