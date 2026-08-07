<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Enums;

enum SyncProcessStep: string
{
    case CREATED = 'CREATED';
    
    case START = 'START';

    case LOAD_DATA = 'LOAD_DATA';

    case CHECK_DEPENDENCIES = 'CHECK_DEPENDENCIES';

    case VALIDATE = 'VALIDATE';

    case MAP_DATA = 'MAP_DATA';

    case FIND_MAPPING = 'FIND_MAPPING';

    case CREATE_EXTERNAL = 'CREATE_EXTERNAL';

    case UPDATE_EXTERNAL = 'UPDATE_EXTERNAL';

    case SAVE_MAPPING = 'SAVE_MAPPING';

    case SAVE_EXTERNAL_ID = 'SAVE_EXTERNAL_ID';

    case AFTER_SYNC = 'AFTER_SYNC';

    case WAITING_DEPENDENCY = 'WAITING_DEPENDENCY';

    case FINISHED = 'FINISHED';
}
