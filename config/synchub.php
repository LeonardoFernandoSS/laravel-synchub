<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | Configurações usadas pelos Jobs de sincronização.
    |
    */

    'queue' => [

        'connection' => env(
            'SYNCHUB_QUEUE_CONNECTION',
            config('queue.default')
        ),

        'name' => env(
            'SYNCHUB_QUEUE',
            'default'
        ),

    ],


    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    */

    'routes' => [

        'enabled' => env(
            'SYNCHUB_ROUTES_ENABLED',
            true
        ),

        'prefix' => env(
            'SYNCHUB_ROUTE_PREFIX',
            'sync'
        ),

        'routes' => [

            'api' => [
                'enabled' => true,
                'prefix' => 'sync',
                'middleware' => ['api'],
            ],

            'web' => [
                'enabled' => true,
                'middleware' => ['web'],
            ],

        ],

    ],


    /*
    |--------------------------------------------------------------------------
    | Sync Process
    |--------------------------------------------------------------------------
    */

    'process' => [

        'retain_logs' => env(
            'SYNCHUB_RETAIN_LOGS',
            true
        ),

    ],


];
