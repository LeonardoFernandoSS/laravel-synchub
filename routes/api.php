<?php

use Illuminate\Support\Facades\Route;
use Synchub\LaravelSynchub\Http\Controllers\SyncController;

Route::prefix('sync')->group(function () {

    Route::post('{entity}', [SyncController::class, 'sync',]);

    Route::post('{entity}/batch', [SyncController::class, 'batch',]);
});
