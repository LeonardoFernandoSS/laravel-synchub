<?php

namespace Synchub\LaravelSynchub\Infrastructure\Registry;

use InvalidArgumentException;
use Synchub\LaravelSynchub\Domain\Sync\Context\SyncContext;

class SyncRegistry
{
    protected array $contexts = [];

    public function register(
        string $name,
        string $context
    ): void {
        $this->contexts[$name] = $context;
    }

    public function context(
        string $name
    ): SyncContext {
        if (!$this->has($name)) {
            throw new InvalidArgumentException(
                "Sync context [$name] not registered."
            );
        }

        return app($this->contexts[$name]);
    }

    public function has(
        string $name
    ): bool {
        return isset($this->contexts[$name]);
    }
}
