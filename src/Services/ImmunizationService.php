<?php

namespace Illimi\Health\Services;

use Illuminate\Support\Facades\Gate;
use Illimi\Health\Events\ImmunizationDue;
use Illimi\Health\Events\HealthEntityChanged;
use Illimi\Health\Models\Immunization;
use Illimi\Health\Resources\ImmunizationResource;
use Illimi\Students\Models\Student;

class ImmunizationService
{
    public function forStudent(string $studentId)
    {
        if (user()) {
            Gate::authorize('viewAny', Immunization::class);
        }

        $this->authorizeParentForStudent($studentId);

        return Immunization::with(['student.parents'])
            ->where('student_id', $studentId)
            ->orderByDesc('due_date')
            ->get();
    }

    public function create(array $data): Immunization
    {
        if (user()) {
            Gate::authorize('create', Immunization::class);
        }

        $record = Immunization::create($data)->load('student');
        event(new HealthEntityChanged('immunization', 'created', (new ImmunizationResource($record))->resolve()));

        return $record;
    }

    public function due()
    {
        if (user()) {
            Gate::authorize('viewDueImmunizations', Immunization::class);
        }

        $days = (int) config('illimi-health.immunization_reminder_days', 14);

        return Immunization::with(['student.parents'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', now()->addDays($days)->toDateString())
            ->orderBy('due_date')
            ->get();
    }

    public function dispatchDueReminders(): int
    {
        $records = $this->due();

        foreach ($records as $record) {
            event(new ImmunizationDue($record));
        }

        return $records->count();
    }

    protected function authorizeParentForStudent(string $studentId): void
    {
        $currentUser = user();
        if (! $currentUser || ! $currentUser->hasRole('parent')) {
            return;
        }

        $allowed = Student::query()
            ->where('id', $studentId)
            ->whereHas('parents', fn ($q) => $q->where('users.id', $currentUser->id))
            ->exists();

        if (! $allowed) {
            abort(403, 'You are not allowed to access this student.');
        }
    }
}
