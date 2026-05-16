@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Immunizations</h1>
            <div>
                <a href="{{ route('home') }}" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Health / Immunizations</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-health-immunization-trigger">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Record Immunization
        </button>
    </div>
    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Vaccine Tracker</h6>
                    <p class="mb-0 text-secondary-light">Due dates and recorded doses stay visible for nurses, admins, and guardians.</p>
                </div>
                <span class="badge bg-info-focus text-info-main" id="healthImmunizationRealtimeStatus">Connected</span>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Vaccine</th>
                            <th>Dose</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="healthImmunizationsTbody">
                        <tr>
                            <td colspan="5" class="text-center text-secondary-light py-24">Loading immunizations…</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const apiBase = @json($apiBase ?? '/api/v1/health');
            const tbody = document.getElementById('healthImmunizationsTbody');
            const status = document.getElementById('healthImmunizationRealtimeStatus');
            const Swal = window.Swal;

            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const setTbody = (html) => {
                if (!tbody) return;
                tbody.innerHTML = html;
            };

            const loadImmunizations = async () => {
                try {
                    Swal?.fire({
                        title: 'Loading immunizations…',
                        allowOutsideClick: false,
                        didOpen: () => Swal?.showLoading(),
                    });
                    const res = await window.axios.get(`${apiBase}/immunizations`, { params: { per_page: 100 } });
                    const list = res?.data?.data?.data || [];
                    const rows = Array.isArray(list) ? list : [];

                    if (rows.length === 0) {
                        setTbody('<tr><td colspan="5" class="text-center text-secondary-light py-24">No immunizations found.</td></tr>');
                        return;
                    }

                    setTbody(rows.map((r) => {
                        const student = r?.student?.full_name || 'Unknown student';
                        const vaccine = r?.vaccine_name || '—';
                        const dose = r?.dose_number ?? '—';
                        const due = r?.due_date || '—';
                        const statusText = r?.status || 'unknown';
                        return `<tr>
                            <td>${escapeHtml(student)}</td>
                            <td>${escapeHtml(vaccine)}</td>
                            <td>${escapeHtml(dose)}</td>
                            <td>${escapeHtml(due)}</td>
                            <td><span class="badge bg-primary-50 text-primary-600">${escapeHtml(statusText)}</span></td>
                        </tr>`;
                    }).join(''));
                } catch (e) {
                    setTbody('<tr><td colspan="5" class="text-center text-danger py-24">Failed to load immunizations.</td></tr>');
                    Swal?.fire({
                        icon: 'error',
                        title: 'Unable to load immunizations',
                        text: e?.response?.data?.message || 'Please refresh and try again.',
                    });
                } finally {
                    Swal?.close();
                }
            };

            window.addEventListener('health:entity.changed', (event) => {
                if (event.detail?.entity !== 'immunization') return;
                if (status) {
                    status.className = 'badge bg-success-focus text-success-main';
                    status.textContent = 'Immunization update received';
                }
                window.setTimeout(() => void loadImmunizations(), 300);
            });

            void loadImmunizations();
        })();
    </script>
@endpush
