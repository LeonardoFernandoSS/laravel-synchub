<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

interface ContextValidator
{
    public function validate(array $entity): void;
}
