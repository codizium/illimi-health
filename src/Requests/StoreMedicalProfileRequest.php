<?php

namespace Illimi\Health\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'blood_group' => ['nullable', 'string', 'max:20'],
            'genotype' => ['nullable', 'string', 'max:20'],
            'allergies' => ['nullable', 'array'],
            'allergies.*' => ['string', 'max:255'],
            'chronic_conditions' => ['nullable', 'array'],
            'chronic_conditions.*' => ['string', 'max:255'],
            'disabilities' => ['nullable', 'array'],
            'disabilities.*' => ['string', 'max:255'],
            'current_medications' => ['nullable', 'array'],
            'current_medications.*' => ['string', 'max:255'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'doctor_phone' => ['nullable', 'string', 'max:30'],
            'health_insurance' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'emergency_contacts' => ['nullable', 'array'],
            'emergency_contacts.*.name' => ['required_with:emergency_contacts', 'string', 'max:255'],
            'emergency_contacts.*.relationship' => ['nullable', 'string', 'max:100'],
            'emergency_contacts.*.phone' => ['required_with:emergency_contacts', 'string', 'max:30'],
            'emergency_contacts.*.alternate_phone' => ['nullable', 'string', 'max:30'],
            'emergency_contacts.*.priority' => ['nullable', 'integer', 'min:1', 'max:10'],
            'emergency_contacts.*.is_primary' => ['nullable', 'boolean'],
        ];
    }
}
