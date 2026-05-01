<?php

namespace Illimi\Health\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EscalateIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'escalated_to' => ['required', 'string', 'exists:users,id'],
            'action_taken' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
