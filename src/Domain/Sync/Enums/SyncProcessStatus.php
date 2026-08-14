<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Enums;

enum SyncProcessStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case WAITING_DEPENDENCY = 'waiting_dependency';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case ERROR = 'error';
    case OBSOLETE = 'obsolete';

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::PENDING => in_array($to, [
                self::PROCESSING,
                self::OBSOLETE,
            ], true),

            self::PROCESSING => in_array($to, [
                self::SUCCESS,
                self::FAILED,
                self::WAITING_DEPENDENCY,
                self::OBSOLETE,
                self::ERROR,
            ], true),

            self::WAITING_DEPENDENCY => in_array($to, [
                self::PROCESSING,
                self::OBSOLETE,
                self::ERROR,
            ], true),

            self::SUCCESS,
            self::FAILED,
            self::ERROR,
            self::OBSOLETE => in_array($to, [
                self::PENDING,
            ], true),
        };
    }

    // public function isActive(): bool
    // {
    //     return match ($this) {
    //         self::PENDING,
    //         self::PROCESSING,
    //         self::WAITING_DEPENDENCY => true,

    //         self::SUCCESS,
    //         self::FAILED,
    //         self::ERROR,
    //         self::OBSOLETE => false,
    //     };
    // }

    // public function isRunnable(): bool
    // {
    //     return match ($this) {
    //         self::PENDING,
    //         self::PROCESSING,
    //         self::WAITING_DEPENDENCY => true,

    //         self::SUCCESS,
    //         self::FAILED,
    //         self::ERROR,
    //         self::OBSOLETE => false,
    //     };
    // }

    // public function isTerminal(): bool
    // {
    //     return !$this->isActive();
    // }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente',
            self::PROCESSING => 'Processando',
            self::WAITING_DEPENDENCY => 'Aguardando Dependência',
            self::SUCCESS => 'Sucesso',
            self::FAILED => 'Falhou',
            self::ERROR => 'Erro',
            self::OBSOLETE => 'Obsoleto',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SUCCESS => 'emerald',
            self::ERROR,
            self::FAILED => 'rose',
            self::PROCESSING => 'amber',
            self::PENDING => 'blue',
            self::WAITING_DEPENDENCY => 'purple',
            self::OBSOLETE => 'slate',
        };
    }
}
