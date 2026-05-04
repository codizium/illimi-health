<?php

namespace Illimi\Health\Services;

use Illuminate\Support\Facades\Gate;
use Illimi\Health\Events\HealthEntityChanged;
use Illimi\Health\Enums\VisitOutcomeEnum;
use Illimi\Health\Events\MedicalVisitLogged;
use Illimi\Health\Events\StudentSentHome;
use Illimi\Health\Models\MedicalVisit;
use Illimi\Health\Resources\MedicalVisitResource;

class MedicalVisitService
{
    public function paginate(array $filters, int $perPage = 15)
    {
        Gate::authorize('viewAny', MedicalVisit::class);

        return MedicalVisit::with(['student', 'attendee'])
            ->when(
                user()?->hasRole('parent') ?? false,
                fn ($query) => $query->whereHas('student.parents', fn ($q) => $q->where('users.id', user()->id))
            )
            ->when($filters['student_id'] ?? null, fn ($query, $studentId) => $query->where('student_id', $studentId))
            ->when($filters['visit_date'] ?? null, fn ($query, $visitDate) => $query->whereDate('visit_date', $visitDate))
            ->latest('visit_date')
            ->paginate(min(max($perPage, 1), 100));
    }

    public function find(string $id): ?MedicalVisit
    {
        $visit = MedicalVisit::with(['student.parents', 'attendee'])->find($id);

        if ($visit) {
            Gate::authorize('view', $visit);
        }

        return $visit;
    }

    public function create(array $data): MedicalVisit
    {
        Gate::authorize('create', MedicalVisit::class);

        if (empty($data['attended_by']) && user()) {
            $data['attended_by'] = user()->id;
        }

        $visit = MedicalVisit::create($data)->load(['student.parents', 'attendee']);

        event(new MedicalVisitLogged($visit));

        if ($visit->outcome === VisitOutcomeEnum::SentHome) {
            event(new StudentSentHome($visit));
        }

        event(new HealthEntityChanged('medical_visit', 'created', (new MedicalVisitResource($visit))->resolve()));

        return $visit;
    }
}
