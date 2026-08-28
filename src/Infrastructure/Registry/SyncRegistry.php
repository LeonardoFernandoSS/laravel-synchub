<?php

namespace Synchub\LaravelSynchub\Infrastructure\Registry;

use InvalidArgumentException;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SourceIdentityResolver;
use Synchub\LaravelSynchub\Domain\Sync\Context\SyncContext;

final class SyncRegistry
{
    protected array $contexts = [];

    public function register(
        string $name,
        string $context,
        string $identityResolver,
    ): void {
        $this->contexts[$name] = [
            'context' => $context,
            'identity_resolver' => $identityResolver,
        ];
    }

    public function context(
        string $name,
    ): SyncContext {
        if (!$this->has($name)) {
            throw new InvalidArgumentException(
                "Sync context [$name] not registered."
            );
        }

        return app(
            $this->contexts[$name]['context']
        );
    }

    public function identityResolver(
        string $name,
    ): SourceIdentityResolver {
        if (!$this->has($name)) {
            throw new InvalidArgumentException(
                "Sync context [$name] not registered."
            );
        }

        return app(
            $this->contexts[$name]['identity_resolver']
        );
    }

    public function has(
        string $name,
    ): bool {
        return isset($this->contexts[$name]);
    }
}
