<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Enums;

enum SyncProcessStatus: string
{
    case PENDING = 'pending';

    case PROCESSING = 'processing';

    case WAITING_DEPENDENCY = 'waiting_dependency';

    case SUCCESS = 'success';

    case FAILED = 'failed';

    case ERROR = 'error';

    case OBSOLETE = 'obsolete';
}
