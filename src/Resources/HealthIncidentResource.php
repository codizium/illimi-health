<?php

namespace Illimi\Health\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HealthIncidentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'incident_date' => $this->incident_date?->format('Y-m-d'),
            'description' => $this->description,
            'severity' => $this->severity?->value,
            'location' => $this->location,
            'witnesses' => $this->witnesses ?? [],
            'action_taken' => $this->action_taken,
            'escalated' => (bool) $this->escalated,
            'escalated_at' => $this->escalated_at?->toIso8601String(),
            'parent_notified' => (bool) $this->parent_notified,
            'notified_at' => $this->notified_at?->toIso8601String(),
            'student' => $this->whenLoaded('student', fn () => [
                'id' => $this->student?->id,
                'full_name' => $this->student?->full_name,
            ]),
            'reported_by' => $this->whenLoaded('reporter', fn () => [
                'id' => $this->reporter?->id,
                'name' => $this->reporter?->name,
            ]),
            'escalated_to' => $this->whenLoaded('escalatedTo', fn () => [
                'id' => $this->escalatedTo?->id,
                'name' => $this->escalatedTo?->name,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
