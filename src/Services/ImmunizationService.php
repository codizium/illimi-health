<?php

namespace Illimi\Health\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illimi\Health\Events\ImmunizationDue;
use Illimi\Health\Events\HealthEntityChanged;
use Illimi\Health\Models\Immunization;
use Illimi\Health\Resources\ImmunizationResource;
use Illimi\Students\Models\Student;

class ImmunizationService
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        if (user()) {
            Gate::authorize('viewAny', Immunization::class);
        }

        $days = (int) config('illimi-health.immunization_reminder_days', 14);
        $dueOnly = filter_var($filters['due_only'] ?? false, FILTER_VALIDATE_BOOL);

        return Immunization::query()
            ->with('student')
            ->when(
                user()?->hasRole('parent') ?? false,
                fn ($query) => $query->whereHas('student.parents', fn ($q) => $q->where('users.id', user()->id))
            )
            ->when($filters['student_id'] ?? null, fn ($query, $studentId) => $query->where('student_id', $studentId))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when(
                $dueOnly,
                fn ($query) => $query
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<=', now()->addDays($days)->toDateString())
            )
            ->orderByDesc('due_date')
            ->paginate(min(max($perPage, 1), 100));
    }

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
