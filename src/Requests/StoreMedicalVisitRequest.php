<?php

namespace Illimi\Health\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illimi\Health\Enums\VisitOutcomeEnum;

class StoreMedicalVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'string', 'exists:illimi_students,id'],
            'attended_by' => ['nullable', 'string', 'exists:users,id'],
            'visit_date' => ['required', 'date'],
            'time_in' => ['required', 'date_format:H:i'],
            'time_out' => ['nullable', 'date_format:H:i', 'after:time_in'],
            'complaint' => ['required', 'string', 'max:5000'],
            'diagnosis' => ['nullable', 'string', 'max:5000'],
            'treatment' => ['nullable', 'string', 'max:5000'],
            'medication_given' => ['nullable', 'array'],
            'medication_given.*' => ['string', 'max:255'],
            'outcome' => ['required', new Enum(VisitOutcomeEnum::class)],
            'follow_up_required' => ['nullable', 'boolean'],
            'follow_up_date' => ['nullable', 'date', 'after_or_equal:visit_date'],
        ];
    }
}
