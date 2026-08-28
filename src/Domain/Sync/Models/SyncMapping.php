<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SyncMapping extends Model
{
    protected $fillable = [
        'source_type',
        'source_key',
        'source_identity',
        'target_key',
        'target_identity',
        'payload_hash',
        'last_payload',
    ];

    protected $casts = [
        'source_identity' => 'array',
        'target_identity' => 'array',
        'last_payload' => 'array',
    ];

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
