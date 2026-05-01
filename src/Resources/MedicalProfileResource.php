<?php

namespace Illimi\Health\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'blood_group' => $this->blood_group,
            'genotype' => $this->genotype,
            'allergies' => $this->allergies ?? [],
            'chronic_conditions' => $this->chronic_conditions ?? [],
            'disabilities' => $this->disabilities ?? [],
            'current_medications' => $this->current_medications ?? [],
            'doctor_name' => $this->doctor_name,
            'doctor_phone' => $this->doctor_phone,
            'health_insurance' => $this->health_insurance,
            'notes' => $this->notes,
            'student' => $this->whenLoaded('student', fn () => [
                'id' => $this->student?->id,
                'full_name' => $this->student?->full_name,
                'admission_number' => $this->student?->admission_number,
            ]),
            'emergency_contacts' => EmergencyContactResource::collection($this->whenLoaded('emergencyContacts')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
