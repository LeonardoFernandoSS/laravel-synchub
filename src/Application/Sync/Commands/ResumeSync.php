<?php

namespace Synchub\LaravelSynchub\Application\Sync\Commands;


final readonly class ResumeSync
{
    public function __construct(
        public int $processId,
    ) {}
}
