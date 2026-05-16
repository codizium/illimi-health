<?php

namespace Illimi\Health\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illimi\Health\Events\HealthEntityChanged;
use Illimi\Health\Models\EmergencyContact;
use Illimi\Health\Models\MedicalProfile;
use Illimi\Health\Resources\MedicalProfileResource;
use Illimi\Students\Models\Student;

class MedicalProfileService
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        Gate::authorize('viewAny', MedicalProfile::class);

        return MedicalProfile::query()
            ->with(['student', 'emergencyContacts'])
            ->when(
                user()?->hasRole('parent') ?? false,
                fn ($query) => $query->whereHas('student.parents', fn ($q) => $q->where('users.id', user()->id))
            )
            ->when($filters['student_id'] ?? null, fn ($query, $studentId) => $query->where('student_id', $studentId))
            ->latest()
            ->paginate(min(max($perPage, 1), 100));
    }

    public function getByStudent(string $studentId): ?MedicalProfile
    {
        Gate::authorize('viewAny', MedicalProfile::class);
        $this->authorizeParentForStudent($studentId);

        $profile = MedicalProfile::with(['student.parents', 'emergencyContacts'])->where('student_id', $studentId)->first();

        if ($profile) {
            Gate::authorize('view', $profile);
        }

        return $profile;
    }

    public function upsert(string $studentId, array $data): MedicalProfile
    {
        Gate::authorize('create', MedicalProfile::class);
        $this->authorizeParentForStudent($studentId);

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

    protected function authorizeParentForStudent(string $studentId): void
    {
        $currentUser = user();
        if (! $currentUser || ! $currentUser->hasRole('parent')) {
            return;
        }

        $allowed = Student::query()
            ->where('id', $studentId)
            ->whereHas('parents', fn ($q) => $q->where('users.id', $currentUser->id))
            ->exists();

        if (! $allowed) {
            abort(403, 'You are not allowed to access this student.');
        }
    }
}
