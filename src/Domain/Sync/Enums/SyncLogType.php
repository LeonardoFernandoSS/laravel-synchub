<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Enums;

enum SyncLogType: string
{
    case TIMELINE = 'timeline';
    case DEBUG = 'debug';
}
