<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SyncMapping extends Model
{
    protected $fillable = [
        'source_type',
        'source_id',
        'target_id',
        'payload_hash',
    ];

    protected $casts = [
        'source_id' => 'integer',
    ];

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}