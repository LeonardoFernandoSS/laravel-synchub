<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Facades;

use Illuminate\Support\Facades\Facade;
use Synchub\LaravelSynchub\Infrastructure\Registry\SyncRegistry;

class Sync extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SyncRegistry::class;
    }
}
