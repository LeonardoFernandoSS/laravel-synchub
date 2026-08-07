<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Exceptions;

use Exception;
use Synchub\LaravelSynchub\Domain\Sync\DTO\SyncDependencyData;

class MissingSyncDependencyException extends Exception
{
    /**
     * @param SyncDependencyData[] $dependencies
     */
    public function __construct(
        public readonly array $dependencies
    ) {
        parent::__construct('Missing sync dependencies.');
    }
}
