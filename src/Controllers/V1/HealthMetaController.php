<?php

namespace Illimi\Health\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illimi\Health\Enums\ImmunizationStatusEnum;
use Illimi\Health\Enums\IncidentSeverityEnum;
use Illimi\Health\Enums\VisitOutcomeEnum;
use Illimi\Health\Models\HealthIncident;
use Illimi\Health\Models\Immunization;
use Illimi\Health\Models\MedicalProfile;
use Illimi\Health\Models\MedicalVisit;
use Illuminate\Support\Facades\Gate;

class HealthMetaController extends BaseController
{
    public function index(): JsonResponse
    {
        Gate::authorize('viewDashboard', MedicalProfile::class);

        return $this->response->success([
            'visitOutcomes' => array_map(
                fn (VisitOutcomeEnum $case) => $case->value,
                VisitOutcomeEnum::cases()
            ),
            'incidentSeverities' => array_map(
                fn (IncidentSeverityEnum $case) => $case->value,
                IncidentSeverityEnum::cases()
            ),
            'immunizationStatuses' => array_map(
                fn (ImmunizationStatusEnum $case) => $case->value,
                ImmunizationStatusEnum::cases()
            ),
        ], 'Health meta retrieved successfully.');
    }

    public function dashboard(): JsonResponse
    {
        Gate::authorize('viewDashboard', MedicalProfile::class);

        $reminderDays = (int) config('illimi-health.immunization_reminder_days', 14);

        return $this->response->success([
            'profileCount' => MedicalProfile::query()->count(),
            'visitCount' => MedicalVisit::query()->count(),
            'incidentCount' => HealthIncident::query()->count(),
            'dueImmunizationsCount' => Immunization::query()
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<=', now()->addDays($reminderDays)->toDateString())
                ->count(),
            'reminderDays' => $reminderDays,
        ], 'Health dashboard retrieved successfully.');
    }
}

