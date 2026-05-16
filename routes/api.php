<?php

use Illuminate\Support\Facades\Route;
use Illimi\Health\Controllers\V1\HealthIncidentController;
use Illimi\Health\Controllers\V1\HealthMetaController;
use Illimi\Health\Controllers\V1\ImmunizationController;
use Illimi\Health\Controllers\V1\MedicalProfileController;
use Illimi\Health\Controllers\V1\MedicalVisitController;

Route::prefix(config('illimi-health.route_prefix', 'api/v1/health'))
    ->name('v1.health.')
    ->middleware(['api', 'auth:sanctum'])
    ->group(function (): void {
        Route::get('meta', [HealthMetaController::class, 'index'])
            ->middleware('throttle:60,1')
            ->name('meta');
        Route::get('dashboard', [HealthMetaController::class, 'dashboard'])
            ->middleware('throttle:60,1')
            ->name('dashboard');

        Route::get('profiles', [MedicalProfileController::class, 'index'])
            ->middleware('throttle:60,1')
            ->name('profiles.index');
        Route::apiResource('profiles', MedicalProfileController::class)
            ->only(['show', 'store'])
            ->middleware('throttle:30,1')
            ->parameters(['profiles' => 'studentId']);
        Route::apiResource('visits', MedicalVisitController::class)
            ->only(['index', 'store', 'show'])
            ->middleware('throttle:60,1');
        Route::apiResource('incidents', HealthIncidentController::class)
            ->only(['index', 'store'])
            ->middleware('throttle:60,1');
        Route::post('/incidents/{id}/escalate', [HealthIncidentController::class, 'escalate'])
            ->middleware('throttle:10,1')
            ->name('incidents.escalate');

        Route::get('/immunizations', [ImmunizationController::class, 'all'])
            ->middleware('throttle:60,1')
            ->name('immunizations.all');
        Route::get('/immunizations/due', [ImmunizationController::class, 'due'])
            ->middleware('throttle:60,1')
            ->name('immunizations.due');
        Route::get('/immunizations/{studentId}', [ImmunizationController::class, 'index'])
            ->middleware('throttle:60,1')
            ->name('immunizations.index');
        Route::post('/immunizations', [ImmunizationController::class, 'store'])
            ->middleware('throttle:30,1')
            ->name('immunizations.store');
    });
