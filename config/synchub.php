<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | Configurações utilizadas pelos Jobs de sincronização.
    |
    */

    'queue' => [

        'connection' => env(
            'SYNCHUB_QUEUE_CONNECTION',
            config('queue.default'),
        ),

        'name' => env(
            'SYNCHUB_QUEUE',
            'default',
        ),

        'tries' => [
            'default' => env(
                'SYNCHUB_QUEUE_TRIES',
                3,
            ),

            'rerun' => env(
                'SYNCHUB_QUEUE_RERUN_TRIES',
                5,
            ),

            'dependency' => env(
                'SYNCHUB_QUEUE_DEPENDENCY_TRIES',
                3,
            ),
        ],

        'backoff' => [
            60,
            300,
            900,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | Configurações das rotas disponibilizadas pelo SyncHub.
    |
    */

    'routes' => [

        'enabled' => env(
            'SYNCHUB_ROUTES_ENABLED',
            true,
        ),

        'prefix' => env(
            'SYNCHUB_ROUTE_PREFIX',
            'sync',
        ),

        'api' => [
            'enabled' => env(
                'SYNCHUB_API_ROUTES_ENABLED',
                true,
            ),

            'prefix' => env(
                'SYNCHUB_API_ROUTE_PREFIX',
                'synchub',
            ),

            'middleware' => [
                'api',
            ],
        ],

        'web' => [
            'enabled' => env(
                'SYNCHUB_WEB_ROUTES_ENABLED',
                true,
            ),

            'middleware' => [
                'web',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Process
    |--------------------------------------------------------------------------
    |
    | Configurações relacionadas à persistência e histórico dos processos
    | de sincronização.
    |
    */

    'process' => [

        'retain_logs' => env(
            'SYNCHUB_RETAIN_LOGS',
            true,
        ),
    ],

    'source_payload' => [
        /*
        |--------------------------------------------------------------------------
        | Source Payload Cache
        |--------------------------------------------------------------------------
        |
        | Defines for how many minutes a previously fetched source payload
        | can be reused by a synchronization process.
        |
        */

        'cache_minutes' => env(
            'SYNCHUB_SOURCE_PAYLOAD_CACHE_MINUTES',
            10
        ),
    ],
];
