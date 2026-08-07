<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

interface GenericMapping
{
    public function externalId(): string;

    public function payloadHash(): string;

    public function target(): string;
}
