<?php

use Illuminate\Support\Facades\Route;
use Illimi\Health\Controllers\V1\HealthIncidentController;
use Illimi\Health\Controllers\V1\ImmunizationController;
use Illimi\Health\Controllers\V1\MedicalProfileController;
use Illimi\Health\Controllers\V1\MedicalVisitController;

Route::prefix(config('illimi-health.route_prefix', 'api/v1/health'))
    ->name('v1.health.')
    ->middleware(['api', 'auth:sanctum', 'organization'])
    ->group(function (): void {
        Route::apiResource('profiles', MedicalProfileController::class)
            ->only(['show', 'store'])
            ->parameters(['profiles' => 'studentId']);
        Route::apiResource('visits', MedicalVisitController::class)->only(['index', 'store', 'show']);
        Route::apiResource('incidents', HealthIncidentController::class)->only(['index', 'store']);
        Route::post('/incidents/{id}/escalate', [HealthIncidentController::class, 'escalate'])->name('incidents.escalate');

        Route::get('/immunizations/due', [ImmunizationController::class, 'due'])->name('immunizations.due');
        Route::get('/immunizations/{studentId}', [ImmunizationController::class, 'index'])->name('immunizations.index');
        Route::post('/immunizations', [ImmunizationController::class, 'store'])->name('immunizations.store');
    });
