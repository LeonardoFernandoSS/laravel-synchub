<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Enums;

enum SyncProcessStep: string
{
    case CREATED = 'CREATED';

    case START = 'START';

    case LOAD_SOURCE_DATA = 'LOAD_SOURCE_DATA';

    case CHECK_DEPENDENCIES = 'CHECK_DEPENDENCIES';

    case VALIDATE_SOURCE = 'VALIDATE_SOURCE';

    case MAP_DATA = 'MAP_DATA';

    case FIND_TARGET_MAPPING = 'FIND_TARGET_MAPPING';

    case CREATE_TARGET = 'CREATE_TARGET';

    case UPDATE_TARGET = 'UPDATE_TARGET';

    case SAVE_TARGET_MAPPING = 'SAVE_TARGET_MAPPING';

    case SAVE_TARGET_ID = 'SAVE_TARGET_ID';

    case AFTER_SYNC = 'AFTER_SYNC';

    case WAITING_DEPENDENCY = 'WAITING_DEPENDENCY';

    case FINISHED = 'FINISHED';

    case OBSOLETE = 'OBSOLETE';

    public function label(): string
    {
        return match ($this) {
            self::CREATED =>
                'Criado',

            self::START =>
                'Iniciando sincronização',

            self::LOAD_SOURCE_DATA =>
                'Carregando dados da origem',

            self::CHECK_DEPENDENCIES =>
                'Verificando dependências',

            self::VALIDATE_SOURCE =>
                'Validando dados da origem',

            self::MAP_DATA =>
                'Mapeando dados',

            self::FIND_TARGET_MAPPING =>
                'Localizando mapeamento do destino',

            self::CREATE_TARGET =>
                'Criando registro no destino',

            self::UPDATE_TARGET =>
                'Atualizando registro no destino',

            self::SAVE_TARGET_MAPPING =>
                'Salvando mapeamento do destino',

            self::SAVE_TARGET_ID =>
                'Salvando ID do destino',

            self::AFTER_SYNC =>
                'Executando pós-sincronização',

            self::WAITING_DEPENDENCY =>
                'Aguardando dependências',

            self::FINISHED =>
                'Sincronização finalizada',

            self::OBSOLETE =>
                'Processo obsoleto',
        };
    }
}