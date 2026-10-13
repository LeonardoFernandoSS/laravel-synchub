<?php

use Illuminate\Support\Facades\Route;
use Synchub\LaravelSynchub\Http\Controllers\DashboardController;
use Synchub\LaravelSynchub\Http\Controllers\RerunController;

Route::get('/synchub', [DashboardController::class, 'index'])
    ->name('synchub.index');

Route::post('/synchub/rerun', [RerunController::class, 'rerunBatch'])
    ->name('synchub.rerun.batch');

Route::get('/synchub/{id}', [DashboardController::class, 'show'])
    ->name('synchub.show');

Route::get('/synchub/{id}/status', [DashboardController::class, 'status'])
    ->name('synchub.status');

Route::post('/synchub/{id}/rerun', [RerunController::class, 'rerun'])
    ->name('synchub.rerun');
