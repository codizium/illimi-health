<?php

namespace Illimi\Health\Controllers\Web;

use Illimi\Health\Enums\ImmunizationStatusEnum;
use Illimi\Health\Enums\IncidentSeverityEnum;
use Illimi\Health\Enums\VisitOutcomeEnum;
use Illimi\Health\Models\HealthIncident;
use Illimi\Health\Models\Immunization;
use Illimi\Health\Models\MedicalProfile;
use Illimi\Health\Models\MedicalVisit;
use Illimi\Students\Models\Student;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class HealthWebController
{
    protected function sharedData(): array
    {
        return [
            'apiBase' => '/api/v1/health',
            'students' => Student::query()
                ->orderBy('first_name')
                ->limit(200)
                ->get(),
            'visitOutcomes' => array_map(fn (VisitOutcomeEnum $case) => $case->value, VisitOutcomeEnum::cases()),
            'incidentSeverities' => array_map(fn (IncidentSeverityEnum $case) => $case->value, IncidentSeverityEnum::cases()),
            'immunizationStatuses' => array_map(fn (ImmunizationStatusEnum $case) => $case->value, ImmunizationStatusEnum::cases()),
        ];
    }

    public function index(): View
    {
        Gate::authorize('viewDashboard', MedicalProfile::class);

        return view('illimi-health::pages.index', $this->sharedData() + [
            'profileCount' => MedicalProfile::query()->count(),
            'visitCount' => MedicalVisit::query()->count(),
            'incidentCount' => HealthIncident::query()->count(),
            'dueImmunizationsCount' => Immunization::query()
                ->whereDate('due_date', '<=', now()->addDays((int) config('illimi-health.immunization_reminder_days', 14))->toDateString())
                ->count(),
        ]);
    }

    public function profiles(): View
    {
        Gate::authorize('viewAny', MedicalProfile::class);

        return view('illimi-health::pages.profiles', $this->sharedData() + [
            'profiles' => MedicalProfile::query()->with(['student', 'emergencyContacts'])->latest()->get(),
        ]);
    }

    public function visits(): View
    {
        Gate::authorize('viewAny', MedicalVisit::class);

        return view('illimi-health::pages.visits', $this->sharedData() + [
            'visits' => MedicalVisit::query()->with(['student', 'attendee'])->latest('visit_date')->get(),
        ]);
    }

    public function incidents(): View
    {
        Gate::authorize('viewAny', HealthIncident::class);

        return view('illimi-health::pages.incidents', $this->sharedData() + [
            'incidents' => HealthIncident::query()->with(['student', 'reporter', 'escalatedTo'])->latest('incident_date')->get(),
        ]);
    }

    public function immunizations(): View
    {
        Gate::authorize('viewAny', Immunization::class);

        return view('illimi-health::pages.immunizations', $this->sharedData() + [
            'immunizations' => Immunization::query()->with('student')->latest('due_date')->get(),
        ]);
    }
}
