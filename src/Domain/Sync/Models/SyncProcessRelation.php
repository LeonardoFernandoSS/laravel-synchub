<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessRelationType;

class SyncProcessRelation extends Model
{
    protected $fillable = [
        'parent_process_id',
        'child_process_id',
        'type',
    ];

    protected $casts = [
        'parent_process_id' => 'integer',
        'child_process_id' => 'integer',
        'type' => SyncProcessRelationType::class,
    ];

    public function parentProcess(): BelongsTo
    {
        return $this->belongsTo(
            SyncProcess::class,
            'parent_process_id',
        );
    }

    public function childProcess(): BelongsTo
    {
        return $this->belongsTo(
            SyncProcess::class,
            'child_process_id',
        );
    }
}