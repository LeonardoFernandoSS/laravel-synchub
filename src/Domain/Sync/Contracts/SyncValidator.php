<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

interface SyncValidator
{
    /**
     * @return array $errors
     */
    public function validate(array $entity): array;
}
