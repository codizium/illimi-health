<?php

namespace Illimi\Health\Services;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illimi\Health\Events\HealthEntityChanged;
use Illimi\Health\Models\EmergencyContact;
use Illimi\Health\Models\MedicalProfile;
use Illimi\Health\Resources\MedicalProfileResource;

class MedicalProfileService
{
    public function getByStudent(string $studentId): ?MedicalProfile
    {
        $profile = MedicalProfile::with(['student.parents', 'emergencyContacts'])->where('student_id', $studentId)->first();

        if ($profile) {
            Gate::authorize('view', $profile);
        }

        return $profile;
    }

    public function upsert(string $studentId, array $data): MedicalProfile
    {
        Gate::authorize('create', MedicalProfile::class);

        return DB::transaction(function () use ($studentId, $data): MedicalProfile {
            $contacts = $data['emergency_contacts'] ?? [];
            unset($data['emergency_contacts']);

            $profile = MedicalProfile::updateOrCreate(
                ['student_id' => $studentId],
                $data + ['student_id' => $studentId]
            );

            if ($contacts !== []) {
                EmergencyContact::where('student_id', $studentId)->delete();

                foreach ($contacts as $index => $contact) {
                    EmergencyContact::create([
                        'student_id' => $studentId,
                        'name' => $contact['name'],
                        'relationship' => $contact['relationship'] ?? null,
                        'phone' => $contact['phone'],
                        'alternate_phone' => $contact['alternate_phone'] ?? null,
                        'priority' => $contact['priority'] ?? ($index + 1),
                        'is_primary' => (bool) ($contact['is_primary'] ?? $index === 0),
                    ]);
                }
            }

            $profile = $profile->load(['student.parents', 'emergencyContacts']);
            event(new HealthEntityChanged('medical_profile', 'upserted', (new MedicalProfileResource($profile))->resolve()));

            return $profile;
        });
    }
}
