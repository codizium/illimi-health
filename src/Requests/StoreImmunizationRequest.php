<?php

namespace Illimi\Health\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illimi\Health\Enums\ImmunizationStatusEnum;

class StoreImmunizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'string', 'exists:illimi_students,id'],
            'vaccine_name' => ['required', 'string', 'max:255'],
            'dose_number' => ['required', 'integer', 'min:1'],
            'date_given' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'administered_by' => ['nullable', 'string', 'max:255'],
            'batch_number' => ['nullable', 'string', 'max:255'],
            'status' => ['required', new Enum(ImmunizationStatusEnum::class)],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
