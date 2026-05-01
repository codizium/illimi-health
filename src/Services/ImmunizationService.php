<?php

namespace Illimi\Health\Services;

use Illuminate\Support\Facades\Gate;
use Illimi\Health\Events\ImmunizationDue;
use Illimi\Health\Events\HealthEntityChanged;
use Illimi\Health\Models\Immunization;
use Illimi\Health\Resources\ImmunizationResource;

class ImmunizationService
{
    public function forStudent(string $studentId)
    {
        if (user()) {
            Gate::authorize('viewAny', Immunization::class);
        }

        return Immunization::with('student')
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
            Gate::authorize('viewAny', Immunization::class);
        }

        $days = (int) config('illimi-health.immunization_reminder_days', 14);

        return Immunization::with('student')
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
}
