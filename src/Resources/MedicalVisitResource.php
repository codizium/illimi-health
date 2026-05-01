<?php

namespace Illimi\Health\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalVisitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'visit_date' => $this->visit_date?->format('Y-m-d'),
            'time_in' => $this->time_in,
            'time_out' => $this->time_out,
            'complaint' => $this->complaint,
            'diagnosis' => $this->diagnosis,
            'treatment' => $this->treatment,
            'medication_given' => $this->medication_given ?? [],
            'outcome' => $this->outcome?->value,
            'parent_notified' => (bool) $this->parent_notified,
            'notified_at' => $this->notified_at?->toIso8601String(),
            'follow_up_required' => (bool) $this->follow_up_required,
            'follow_up_date' => $this->follow_up_date?->format('Y-m-d'),
            'student' => $this->whenLoaded('student', fn () => [
                'id' => $this->student?->id,
                'full_name' => $this->student?->full_name,
            ]),
            'attended_by' => $this->whenLoaded('attendee', fn () => [
                'id' => $this->attendee?->id,
                'name' => $this->attendee?->name,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
