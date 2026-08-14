<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Enums;

enum SyncProcessRelationType: string
{
    case TRIGGERED = 'triggered';

    case DEPENDENCY = 'dependency';

    case RERUN = 'rerun';

    public function label(): string
    {
        return match ($this) {
            self::TRIGGERED => 'Disparo',
            self::DEPENDENCY => 'Dependência',
            self::RERUN => 'Reexecução',
        };
    }
}