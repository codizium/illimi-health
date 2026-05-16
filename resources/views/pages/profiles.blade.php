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
                    <tbody id="healthProfilesTbody">
                        <tr>
                            <td colspan="5" class="text-center text-secondary-light py-24">Loading profiles…</td>
                        </tr>
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
                            <input type="hidden" name="student_id" required>
                            <div class="position-relative">
                                <input type="text" class="form-control" name="student_search" placeholder="Search student by name/admission number…" autocomplete="off" required>
                                <div class="list-group position-absolute w-100 shadow-sm d-none" style="z-index: 1060; max-height: 260px; overflow:auto;" id="healthStudentSearchResults"></div>
                            </div>
                            <div class="form-text">Type at least 2 characters to search.</div>
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
            const apiBase = @json($apiBase ?? '/api/v1/health');
            const modalElement = document.getElementById('healthProfileModal');
            const form = document.getElementById('healthProfileForm');
            const status = document.getElementById('healthProfileRealtimeStatus');
            const modal = modalElement ? new bootstrap.Modal(modalElement) : null;
            const tbody = document.getElementById('healthProfilesTbody');
            const studentSearchInput = form?.querySelector('input[name="student_search"]');
            const studentIdInput = form?.querySelector('input[name="student_id"]');
            const results = document.getElementById('healthStudentSearchResults');

            const Swal = window.Swal;

            document.querySelector('.js-health-profile-trigger')?.addEventListener('click', () => modal?.show());

            const setTbody = (html) => {
                if (!tbody) return;
                tbody.innerHTML = html;
            };

            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const loadProfiles = async () => {
                try {
                    Swal?.fire({
                        title: 'Loading profiles…',
                        allowOutsideClick: false,
                        didOpen: () => Swal?.showLoading(),
                    });
                    const res = await window.axios.get(`${apiBase}/profiles`, { params: { per_page: 100 } });
                    const list = res?.data?.data?.data || [];

                    if (!Array.isArray(list) || list.length === 0) {
                        setTbody('<tr><td colspan="5" class="text-center text-secondary-light py-24">No profiles found.</td></tr>');
                        return;
                    }

                    setTbody(list.map((p) => {
                        const studentName = p?.student?.full_name || 'Unknown student';
                        const blood = p?.blood_group || '—';
                        const allergies = Array.isArray(p?.allergies) ? p.allergies.filter(Boolean).join(', ') : '—';
                        const doctor = p?.doctor_name || '—';
                        const emergencyCount = Array.isArray(p?.emergency_contacts) ? p.emergency_contacts.length : 0;
                        return `<tr>
                            <td>${escapeHtml(studentName)}</td>
                            <td>${escapeHtml(blood)}</td>
                            <td>${escapeHtml(allergies || '—')}</td>
                            <td>${escapeHtml(doctor)}</td>
                            <td>${escapeHtml(emergencyCount)}</td>
                        </tr>`;
                    }).join(''));
                } catch (e) {
                    setTbody('<tr><td colspan="5" class="text-center text-danger py-24">Failed to load profiles.</td></tr>');
                    Swal?.fire({
                        icon: 'error',
                        title: 'Unable to load profiles',
                        text: e?.response?.data?.message || 'Please refresh and try again.',
                    });
                } finally {
                    Swal?.close();
                }
            };

            form?.addEventListener('submit', async (event) => {
                event.preventDefault();
                const formData = new FormData(form);
                const studentId = formData.get('student_id');
                if (!studentId) {
                    Swal?.fire({ icon: 'warning', title: 'Select a student', text: 'Search and pick a student before saving.' });
                    return;
                }
                const payload = {
                    blood_group: formData.get('blood_group') || null,
                    genotype: formData.get('genotype') || null,
                    allergies: String(formData.get('allergies_text') || '').split(',').map(item => item.trim()).filter(Boolean),
                    current_medications: String(formData.get('current_medications_text') || '').split(',').map(item => item.trim()).filter(Boolean),
                    doctor_name: formData.get('doctor_name') || null,
                    doctor_phone: formData.get('doctor_phone') || null,
                    notes: formData.get('notes') || null,
                };

                try {
                    Swal?.fire({ title: 'Saving profile…', allowOutsideClick: false, didOpen: () => Swal?.showLoading() });
                    const response = await window.axios.post(`${apiBase}/profiles/${studentId}`, payload);
                    if (response?.status >= 200 && response?.status < 300) {
                        modal?.hide();
                        Swal?.fire({ icon: 'success', title: 'Profile saved', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
                        await loadProfiles();
                    }
                } catch (e) {
                    Swal?.fire({ icon: 'error', title: 'Save failed', text: e?.response?.data?.message || 'Failed to save profile.' });
                } finally {
                    Swal?.close();
                }
            });

            window.addEventListener('health:entity.changed', (event) => {
                if (event.detail?.entity !== 'medical_profile') return;
                if (status) {
                    status.className = 'badge bg-success-focus text-success-main';
                    status.textContent = 'Profile update received';
                }
                window.setTimeout(() => void loadProfiles(), 300);
            });

            // Student search (uses Students module API)
            let searchTimer = null;
            const hideResults = () => {
                results?.classList.add('d-none');
                if (results) results.innerHTML = '';
            };

            studentSearchInput?.addEventListener('input', () => {
                const q = String(studentSearchInput.value || '').trim();
                studentIdInput.value = '';
                hideResults();

                if (searchTimer) window.clearTimeout(searchTimer);
                if (q.length < 2) return;

                searchTimer = window.setTimeout(async () => {
                    try {
                        const res = await window.axios.get('/api/v1/students', { params: { per_page: 10, search: q } });
                        const list = res?.data?.data?.data || res?.data?.data || [];
                        const students = Array.isArray(list) ? list : [];
                        if (!results) return;
                        if (students.length === 0) {
                            results.innerHTML = '<div class="list-group-item text-secondary-light">No students found</div>';
                            results.classList.remove('d-none');
                            return;
                        }
                        results.innerHTML = students.map((s) => {
                            const label = `${s.full_name || s.name || 'Student'}${s.admission_number ? ` • ${s.admission_number}` : ''}`;
                            return `<button type="button" class="list-group-item list-group-item-action" data-student-id="${escapeHtml(s.id)}" data-student-label="${escapeHtml(label)}">${escapeHtml(label)}</button>`;
                        }).join('');
                        results.classList.remove('d-none');
                    } catch (e) {
                        // ignore noisy failures
                    }
                }, 250);
            });

            results?.addEventListener('click', (e) => {
                const btn = e.target?.closest?.('[data-student-id]');
                if (!btn) return;
                const id = btn.getAttribute('data-student-id') || '';
                const label = btn.getAttribute('data-student-label') || '';
                if (studentIdInput) studentIdInput.value = id;
                if (studentSearchInput) studentSearchInput.value = label;
                hideResults();
            });

            document.addEventListener('click', (e) => {
                if (!results || results.classList.contains('d-none')) return;
                if (results.contains(e.target) || studentSearchInput?.contains(e.target)) return;
                hideResults();
            });

            void loadProfiles();
        })();
    </script>
@endpush
