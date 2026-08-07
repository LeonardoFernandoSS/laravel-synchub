<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Exceptions;

use Exception;

class BusinessException extends Exception
{
    public function __construct(
        string $message,
        public array $errors = []
    ) {
        parent::__construct($message);
    }
}
