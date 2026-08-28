<?php

declare(strict_types=1);

namespace Synchub\LaravelSynchub\Domain\Sync\ValueObjects;

use InvalidArgumentException;

final readonly class TargetIdentity extends SyncIdentity
{
    /**
     * @param array<string, int|string> $values
     */
    public static function from(array $values): self
    {
        if ($values === []) {
            throw new InvalidArgumentException(
                'Target identity cannot be empty.'
            );
        }

        foreach ($values as $key => $value) {
            if ($key === '') {
                throw new InvalidArgumentException(
                    'Target identity keys cannot be empty.'
                );
            }

            if (!is_int($value) && !is_string($value)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Target identity value [%s] must be an integer or string.',
                        $key,
                    )
                );
            }

            if ($value === '') {
                throw new InvalidArgumentException(
                    sprintf(
                        'Target identity value [%s] cannot be empty.',
                        $key,
                    )
                );
            }
        }

        ksort($values);

        return new self($values);
    }
}
