<?php

namespace Illimi\Health\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImmunizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'vaccine_name' => $this->vaccine_name,
            'dose_number' => $this->dose_number,
            'date_given' => $this->date_given?->format('Y-m-d'),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'administered_by' => $this->administered_by,
            'batch_number' => $this->batch_number,
            'status' => $this->status?->value,
            'notes' => $this->notes,
            'student' => $this->whenLoaded('student', fn () => [
                'id' => $this->student?->id,
                'full_name' => $this->student?->full_name,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
