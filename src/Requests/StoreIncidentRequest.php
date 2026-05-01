<?php

namespace Illimi\Health\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illimi\Health\Enums\IncidentSeverityEnum;

class StoreIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'string', 'exists:illimi_students,id'],
            'reported_by' => ['nullable', 'string', 'exists:users,id'],
            'incident_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:5000'],
            'severity' => ['required', new Enum(IncidentSeverityEnum::class)],
            'location' => ['nullable', 'string', 'max:255'],
            'witnesses' => ['nullable', 'array'],
            'witnesses.*' => ['string', 'max:255'],
            'action_taken' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
