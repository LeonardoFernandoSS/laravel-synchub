<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Models;


use Illuminate\Database\Eloquent\Model;


class SyncDependency extends Model
{

    protected $fillable=[

        'sync_process_id',

        'depends_on_process_id',

        'context',

        'context_id',

        'resolved',

        'resolved_at'

    ];



    protected $casts=[

        'resolved'=>'boolean',

        'resolved_at'=>'datetime',

    ];



    public function process()
    {
        return $this->belongsTo(
            SyncProcess::class
        );
    }



    public function dependencyProcess()
    {
        return $this->belongsTo(
            SyncProcess::class,
            'depends_on_process_id'
        );
    }

}
