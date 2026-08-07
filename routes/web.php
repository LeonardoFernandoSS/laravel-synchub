<?php

use Illuminate\Support\Facades\Route;
use Synchub\LaravelSynchub\Http\Controllers\SyncController;

Route::get('/sync-processes', [SyncController::class, 'index'])
    ->name('sync-processes.index');

Route::get('/sync-processes/{id}', [SyncController::class, 'show'])
    ->name('sync-processes.show');

Route::post('/sync-processes/{id}/rerun', [SyncController::class, 'rerun'])
    ->name('sync-processes.rerun');
