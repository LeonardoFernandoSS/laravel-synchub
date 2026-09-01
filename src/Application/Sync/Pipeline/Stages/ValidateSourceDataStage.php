<?php

namespace Synchub\LaravelSynchub\Application\Sync\Pipeline\Stages;

use Closure;
use Synchub\LaravelSynchub\Application\Sync\Pipeline\SyncExecution;
use Synchub\LaravelSynchub\Application\Sync\Services\SyncProcessService;
use Synchub\LaravelSynchub\Domain\Sync\Contracts\SyncStage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessMessage;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;
use Synchub\LaravelSynchub\Domain\Sync\Exceptions\BusinessException;

class ValidateSourceDataStage implements SyncStage
{
    public function __construct(
        private SyncProcessService $processService,
    ) {}

    public function handle(
        SyncExecution $execution,
        Closure $next,
    ): mixed {
        $process = $execution->process;

        $this->processService->step(
            $process,
            SyncProcessStep::VALIDATE_SOURCE,
            SyncProcessMessage::DATA_VALIDATING,
        );

        $validator = $execution->context->validator;

        if ($validator === null) {
            return $next($execution);
        }

        $errors = $validator->validate(
            $execution->sourcePayload,
            $execution->mapping->lastPayload
        );

        if (!empty($errors)) {
            $this->processService->log(
                $process,
                SyncProcessMessage::DATA_VALIDATED,
                [
                    'valid' => false,
                    'errors' => $errors,
                ],
            );

            $this->processService->invalidateSourcePayload(
                $process,
            );

            throw new BusinessException(
                'Falha na validação dos dados de origem.',
                $errors,
            );
        }

        $this->processService->log(
            $process,
            SyncProcessMessage::DATA_VALIDATED,
            [
                'valid' => true,
            ],
        );

        return $next($execution);
    }
}
