<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Perguruan\Presentation\Controllers\Api\PerguruanController;

Route::middleware(['api', 'auth:api', 'perguruan'])
    ->prefix('api/v1/perguruan')
    ->name('api.v1.perguruan.')
    ->group(function (): void {
        Route::get('/', [PerguruanController::class, 'index'])->name('index');
        Route::post('/', [PerguruanController::class, 'store'])->name('store');
        Route::get('/{perguruan}', [PerguruanController::class, 'show'])->name('show');
        Route::put('/{perguruan}', [PerguruanController::class, 'update'])->name('update');
        Route::delete('/{perguruan}', [PerguruanController::class, 'destroy'])->name('destroy');
    });
