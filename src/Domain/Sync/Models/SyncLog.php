<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Models;


use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncLogType;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;


use Illuminate\Database\Eloquent\Model;


class SyncLog extends Model
{

    protected $fillable=[

        'sync_process_id',
        'step',
        'message',
        'payload',
        'type'

    ];



    protected $casts=[

        'step'=>SyncProcessStep::class,

        'payload'=>'array',

        'type'=>SyncLogType::class,

    ];



    public function process()
    {
        return $this->belongsTo(
            SyncProcess::class
        );
    }

}
