<?php

namespace Synchub\LaravelSynchub\Domain\Sync\Models;


use Synchub\LaravelSynchub\Domain\Sync\Contracts\GenericMapping;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class Mapping extends Model implements GenericMapping
{


    protected $fillable=[

        'mappable_type',

        'mappable_id',

        'external_id',

        'external_system',

        'payload_hash'

    ];



    protected $casts=[

        'mappable_id'=>'integer'

    ];



    public function mappable(): MorphTo
    {
        return $this->morphTo();
    }



    public function externalId(): string
    {
        return $this->external_id;
    }



    public function payloadHash(): string
    {
        return $this->payload_hash;
    }



    public function target(): string
    {
        return $this->external_system;
    }

}
