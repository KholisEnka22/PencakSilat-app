<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Modules\Perguruan\Presentation\Controllers\PerguruanController;

Route::middleware(['web', 'auth', 'perguruan'])
    ->prefix('perguruan')
    ->name('perguruan.')
    ->group(function () {
        Route::get('/',             [PerguruanController::class, 'index'])   ->name('index');
        Route::get('/create',       [PerguruanController::class, 'create'])  ->name('create');
        Route::post('/',            [PerguruanController::class, 'store'])   ->name('store');
        Route::get('/{perguruan}',      [PerguruanController::class, 'show'])    ->name('show');
        Route::get('/{perguruan}/edit', [PerguruanController::class, 'edit'])    ->name('edit');
        Route::put('/{perguruan}',      [PerguruanController::class, 'update'])  ->name('update');
        Route::delete('/{perguruan}',   [PerguruanController::class, 'destroy']) ->name('destroy');
    });