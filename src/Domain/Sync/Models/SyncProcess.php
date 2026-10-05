<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;

class SyncProcess extends Model
{
    protected $fillable = [
        'context',

        'source_key',
        'source_identity',

        'force',

        'status',
        'current_step',

        'source_payload',
        'source_payload_provided',
        'target_payload',
        'target_response',

        'error',

        'payload_cached_at',

        'started_at',
        'finished_at',
        'resumed_at',
    ];

    protected $casts = [
        'status' => SyncProcessStatus::class,
        'current_step' => SyncProcessStep::class,

        'force' => 'boolean',

        'source_identity' => 'array',

        'source_payload' => 'array',
        'source_payload_provided' => 'boolean',
        'target_payload' => 'array',
        'target_response' => 'array',
        'error' => 'array',

        'payload_cached_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'resumed_at' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(
            SyncLog::class,
        );
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(
            SyncDependency::class,
        );
    }

    public function dependentProcesses(): HasMany
    {
        return $this->hasMany(
            SyncDependency::class,
            'depends_on_process_id',
        );
    }

    public function triggeredRelations(): HasMany
    {
        return $this->hasMany(
            SyncProcessRelation::class,
            'parent_process_id',
        );
    }

    public function parentRelations(): HasMany
    {
        return $this->hasMany(
            SyncProcessRelation::class,
            'child_process_id',
        );
    }
}
