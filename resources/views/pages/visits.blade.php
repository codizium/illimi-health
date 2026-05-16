@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Medical Visits</h1>
            <div>
                <a href="{{ route('home') }}" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Health / Visits</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-health-visit-trigger">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Log Visit
        </button>
    </div>
    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Sick Bay Register</h6>
                    <p class="mb-0 text-secondary-light">Track complaints, treatment, follow-up dates, and sent-home outcomes.</p>
                </div>
                <span class="badge bg-primary-50 text-primary-600" id="healthVisitRealtimeStatus">Live feed ready</span>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Date</th>
                            <th>Complaint</th>
                            <th>Outcome</th>
                            <th>Parent Notified</th>
                        </tr>
                    </thead>
                    <tbody id="healthVisitsTbody">
                        <tr>
                            <td colspan="5" class="text-center text-secondary-light py-24">Loading visits…</td>
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
            const tbody = document.getElementById('healthVisitsTbody');
            const status = document.getElementById('healthVisitRealtimeStatus');
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

            const loadVisits = async () => {
                try {
                    Swal?.fire({
                        title: 'Loading visits…',
                        allowOutsideClick: false,
                        didOpen: () => Swal?.showLoading(),
                    });
                    const res = await window.axios.get(`${apiBase}/visits`, { params: { per_page: 50 } });
                    const list = res?.data?.data?.data?.data || res?.data?.data?.data || [];
                    const rows = Array.isArray(list) ? list : [];

                    if (rows.length === 0) {
                        setTbody('<tr><td colspan="5" class="text-center text-secondary-light py-24">No visits found.</td></tr>');
                        return;
                    }

                    setTbody(rows.map((v) => {
                        const student = v?.student?.full_name || 'Unknown student';
                        const date = v?.visit_date || '—';
                        const complaint = v?.complaint || '—';
                        const outcome = String(v?.outcome || '—').replaceAll('_', ' ');
                        const notified = !!v?.parent_notified;
                        const badgeClass = notified ? 'bg-success-focus text-success-main' : 'bg-warning-focus text-warning-main';
                        const badgeText = notified ? 'Yes' : 'Pending';
                        return `<tr>
                            <td>${escapeHtml(student)}</td>
                            <td>${escapeHtml(date)}</td>
                            <td>${escapeHtml(complaint)}</td>
                            <td>${escapeHtml(outcome)}</td>
                            <td><span class="badge ${badgeClass}">${escapeHtml(badgeText)}</span></td>
                        </tr>`;
                    }).join(''));
                } catch (e) {
                    setTbody('<tr><td colspan="5" class="text-center text-danger py-24">Failed to load visits.</td></tr>');
                    Swal?.fire({
                        icon: 'error',
                        title: 'Unable to load visits',
                        text: e?.response?.data?.message || 'Please refresh and try again.',
                    });
                } finally {
                    Swal?.close();
                }
            };

            window.addEventListener('health:entity.changed', (event) => {
                if (event.detail?.entity !== 'medical_visit') return;
                if (status) {
                    status.className = 'badge bg-success-focus text-success-main';
                    status.textContent = 'Visit update received';
                }
                window.setTimeout(() => void loadVisits(), 300);
            });

            void loadVisits();
        })();
    </script>
@endpush
