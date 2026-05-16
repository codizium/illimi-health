<?php

use Illuminate\Support\Facades\Route;
use Illimi\Health\Controllers\Web\HealthWebController;

Route::middleware(['web', 'auth'])
    ->prefix('health')
    ->name('health.')
    ->group(function (): void {
        Route::middleware('core.role:admin|super-admin|principal')->group(function () {
            Route::get('/', [HealthWebController::class, 'index'])->name('index');
            Route::get('/profiles', [HealthWebController::class, 'profiles'])->name('profiles');
            Route::get('/visits', [HealthWebController::class, 'visits'])->name('visits');
            Route::get('/incidents', [HealthWebController::class, 'incidents'])->name('incidents');
            Route::get('/immunizations', [HealthWebController::class, 'immunizations'])->name('immunizations');
        });
    });
