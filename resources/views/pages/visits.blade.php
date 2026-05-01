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
                    <tbody>
                        @foreach ($visits as $visit)
                            <tr>
                                <td>{{ $visit->student?->full_name ?? 'Unknown student' }}</td>
                                <td>{{ $visit->visit_date?->format('d M Y') }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($visit->complaint, 70) }}</td>
                                <td>{{ str_replace('_', ' ', $visit->outcome?->value ?? '—') }}</td>
                                <td>
                                    <span class="badge {{ $visit->parent_notified ? 'bg-success-focus text-success-main' : 'bg-warning-focus text-warning-main' }}">
                                        {{ $visit->parent_notified ? 'Yes' : 'Pending' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
