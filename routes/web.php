<?php

use Illuminate\Support\Facades\Route;
use Synchub\LaravelSynchub\Http\Controllers\SyncController;

Route::get('/synchub', [SyncController::class, 'index'])
    ->name('synchub.index');

Route::post('/synchub/rerun', [SyncController::class, 'rerunBatch'])
    ->name('synchub.rerun.batch');

Route::get('/synchub/{id}', [SyncController::class, 'show'])
    ->name('synchub.show');

Route::get('/synchub/{id}/status', [SyncController::class, 'status'])
    ->name('synchub.status');

Route::post('/synchub/{id}/rerun', [SyncController::class, 'rerun'])
    ->name('synchub.rerun');
