<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Models;


use Illuminate\Database\Eloquent\Model;


class SyncProcessRelation extends Model
{

    protected $fillable=[

        'parent_process_id',

        'child_process_id',

        'type'

    ];



    public function parentProcess()
    {
        return $this->belongsTo(
            SyncProcess::class,
            'parent_process_id'
        );
    }



    public function childProcess()
    {
        return $this->belongsTo(
            SyncProcess::class,
            'child_process_id'
        );
    }

}
