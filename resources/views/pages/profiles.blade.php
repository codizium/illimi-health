@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Medical Profiles</h1>
            <div>
                <a href="{{ route('home') }}" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Health / Profiles</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-health-profile-trigger">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Add Profile
        </button>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Student Medical Profiles</h6>
                    <p class="mb-0 text-secondary-light">Private health records stay encrypted at rest and sync across open tabs in realtime.</p>
                </div>
                <span class="badge bg-danger-focus text-danger-main" id="healthProfileRealtimeStatus">Realtime ready</span>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="healthProfilesTable">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Blood Group</th>
                            <th>Allergies</th>
                            <th>Doctor</th>
                            <th>Emergency Contacts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($profiles as $profile)
                            <tr>
                                <td>{{ $profile->student?->full_name ?? 'Unknown student' }}</td>
                                <td>{{ $profile->blood_group ?: '—' }}</td>
                                <td>{{ collect($profile->allergies ?? [])->filter()->join(', ') ?: '—' }}</td>
                                <td>{{ $profile->doctor_name ?: '—' }}</td>
                                <td>{{ $profile->emergencyContacts->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="healthProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Medical Profile</h5>
                        <p class="mb-0 text-sm text-secondary-light">Create or update a student's encrypted medical record.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="healthProfileForm" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Student</label>
                            <select class="form-select" name="student_id" required>
                                <option value="">Select student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Blood Group</label>
                            <input type="text" class="form-control" name="blood_group">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Genotype</label>
                            <input type="text" class="form-control" name="genotype">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Allergies</label>
                            <input type="text" class="form-control" name="allergies_text" placeholder="Comma separated">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Current Medications</label>
                            <input type="text" class="form-control" name="current_medications_text" placeholder="Comma separated">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Doctor Name</label>
                            <input type="text" class="form-control" name="doctor_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Doctor Phone</label>
                            <input type="text" class="form-control" name="doctor_phone">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="healthProfileForm" class="btn btn-primary-600">Save Profile</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const modalElement = document.getElementById('healthProfileModal');
            const form = document.getElementById('healthProfileForm');
            const status = document.getElementById('healthProfileRealtimeStatus');
            const modal = modalElement ? new bootstrap.Modal(modalElement) : null;

            document.querySelector('.js-health-profile-trigger')?.addEventListener('click', () => modal?.show());

            form?.addEventListener('submit', async (event) => {
                event.preventDefault();
                const formData = new FormData(form);
                const studentId = formData.get('student_id');
                const payload = {
                    blood_group: formData.get('blood_group') || null,
                    genotype: formData.get('genotype') || null,
                    allergies: String(formData.get('allergies_text') || '').split(',').map(item => item.trim()).filter(Boolean),
                    current_medications: String(formData.get('current_medications_text') || '').split(',').map(item => item.trim()).filter(Boolean),
                    doctor_name: formData.get('doctor_name') || null,
                    doctor_phone: formData.get('doctor_phone') || null,
                    notes: formData.get('notes') || null,
                };

                const response = await window.axios.post(`/api/v1/health/profiles/${studentId}`, payload);
                if (response?.status >= 200 && response?.status < 300) {
                    modal?.hide();
                    window.location.reload();
                }
            });

            window.addEventListener('health:entity.changed', (event) => {
                if (event.detail?.entity !== 'medical_profile') return;
                if (status) {
                    status.className = 'badge bg-success-focus text-success-main';
                    status.textContent = 'Profile update received';
                }
                window.setTimeout(() => window.location.reload(), 700);
            });
        })();
    </script>
@endpush
