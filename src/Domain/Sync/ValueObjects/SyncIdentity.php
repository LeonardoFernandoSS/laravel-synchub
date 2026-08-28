<?php

declare(strict_types=1);

namespace Synchub\LaravelSynchub\Domain\Sync\ValueObjects;

use InvalidArgumentException;

abstract readonly class SyncIdentity
{
    /**
     * @param array<string, int|string> $values
     */
    protected function __construct(
        private array $values,
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function values(): array
    {
        return $this->values;
    }

    public function value(string $key): int|string|null
    {
        return $this->values[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * Retorna uma representação determinística da identidade.
     *
     * Ex:
     *
     * [
     *     'product_id' => 123,
     *     'category_id' => 45,
     * ]
     *
     * =>
     *
     * category_id=45|product_id=123
     */
    public function key(): string
    {
        $parts = [];

        foreach ($this->values as $key => $value) {
            $parts[] = sprintf(
                '%s=%s',
                rawurlencode($key),
                rawurlencode((string) $value),
            );
        }

        return implode('|', $parts);
    }

    public function equals(self $other): bool
    {
        return $this->values === $other->values;
    }
}
