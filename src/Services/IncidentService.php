<?php

namespace Illimi\Health\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illimi\Health\Events\HealthEntityChanged;
use Illimi\Health\Enums\IncidentSeverityEnum;
use Illimi\Health\Events\IncidentEscalated;
use Illimi\Health\Events\IncidentReported;
use Illimi\Health\Exceptions\IncidentEscalationException;
use Illimi\Health\Models\HealthIncident;
use Illimi\Health\Resources\HealthIncidentResource;

class IncidentService
{
    public function paginate(int $perPage = 15)
    {
        Gate::authorize('viewAny', HealthIncident::class);

        return HealthIncident::with(['student', 'reporter', 'escalatedTo'])
            ->when(
                user()?->hasRole('parent') ?? false,
                fn ($query) => $query->whereHas('student.parents', fn ($q) => $q->where('users.id', user()->id))
            )
            ->latest('incident_date')
            ->paginate(min(max($perPage, 1), 100));
    }

    public function create(array $data): HealthIncident
    {
        Gate::authorize('create', HealthIncident::class);

        if (empty($data['reported_by']) && user()) {
            $data['reported_by'] = user()->id;
        }

        $incident = HealthIncident::create($data)->load(['student.parents', 'reporter', 'escalatedTo']);

        event(new IncidentReported($incident));
        event(new HealthEntityChanged('incident', 'created', (new HealthIncidentResource($incident))->resolve()));

        if (in_array($incident->severity, [IncidentSeverityEnum::Severe, IncidentSeverityEnum::Critical], true)) {
            $incident->forceFill([
                'escalated' => true,
                'escalated_at' => now(),
            ])->save();

            $incident->load(['student.parents', 'reporter', 'escalatedTo']);

            event(new IncidentEscalated($incident));
        }

        return $incident;
    }

    public function escalate(string $id, array $data, bool $authorize = true): HealthIncident
    {
        if ($authorize) {
            Gate::authorize('escalate', HealthIncident::class);
        }

        return DB::transaction(function () use ($id, $data): HealthIncident {
            $incident = HealthIncident::lockForUpdate()->find($id);

            if (!$incident) {
                throw new IncidentEscalationException('Incident not found.');
            }

            if (empty($data['escalated_to'])) {
                throw new IncidentEscalationException('Escalation target is required.');
            }

            $incident->forceFill([
                'escalated' => true,
                'escalated_to' => $data['escalated_to'],
                'escalated_at' => now(),
                'action_taken' => $data['action_taken'] ?? $incident->action_taken,
            ])->save();

            $incident->load(['student.parents', 'reporter', 'escalatedTo']);

            event(new IncidentEscalated($incident));
            event(new HealthEntityChanged('incident', 'escalated', (new HealthIncidentResource($incident))->resolve()));

            return $incident;
        });
    }
}
