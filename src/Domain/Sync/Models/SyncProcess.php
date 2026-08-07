<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Models;

use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class SyncProcess extends Model
{

    protected $fillable = [

        'type',
        'context',
        'context_id',

        'force',

        'status',
        'current_step',

        'internal_payload',
        'mapped_payload',
        'external_response',

        'error',

        'payload_cached_at',

        'started_at',
        'finished_at',

    ];


    protected $casts = [

        'status'=>SyncProcessStatus::class,

        'current_step'=>SyncProcessStep::class,

        'force'=>'boolean',

        'internal_payload'=>'array',
        'mapped_payload'=>'array',
        'external_response'=>'array',
        'error'=>'array',

        'payload_cached_at'=>'datetime',
        'started_at'=>'datetime',
        'finished_at'=>'datetime',

    ];



    public function logs(): HasMany
    {
        return $this->hasMany(
            SyncLog::class
        );
    }



    public function dependencies(): HasMany
    {
        return $this->hasMany(
            SyncDependency::class
        );
    }



    public function dependentProcesses()
    {
        return $this->hasMany(
            SyncDependency::class,
            'depends_on_process_id'
        );
    }



    public function triggeredRelations()
    {
        return $this->hasMany(
            SyncProcessRelation::class,
            'parent_process_id'
        );
    }



    public function parentRelations()
    {
        return $this->hasMany(
            SyncProcessRelation::class,
            'child_process_id'
        );
    }



    public function isObsolete(): bool
    {
        return $this->status === SyncProcessStatus::OBSOLETE;
    }

}
