<?php

declare(strict_types=1);

namespace Synchub\LaravelSynchub\Domain\Sync\Contracts;

use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;

interface SourceIdentityResolver
{
    public function resolve(
        mixed $source,
    ): SourceIdentity;

    /**
     * @param array<mixed> $sources
     * @return array<SourceIdentity>
     */
    public function resolveMany(array $sources): array;
}
