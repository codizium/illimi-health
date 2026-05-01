@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Health Incidents</h1>
            <div>
                <a href="{{ route('home') }}" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Health / Incidents</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-health-incident-trigger">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Report Incident
        </button>
    </div>
    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Incident Escalation Log</h6>
                    <p class="mb-0 text-secondary-light">Severe incidents are highlighted for quick escalation and management follow-up.</p>
                </div>
                <span class="badge bg-danger-focus text-danger-main" id="healthIncidentRealtimeStatus">Realtime ready</span>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Date</th>
                            <th>Severity</th>
                            <th>Description</th>
                            <th>Escalation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($incidents as $incident)
                            <tr>
                                <td>{{ $incident->student?->full_name ?? 'Unknown student' }}</td>
                                <td>{{ $incident->incident_date?->format('d M Y') }}</td>
                                <td><span class="badge {{ in_array($incident->severity?->value, ['critical', 'severe'], true) ? 'bg-danger-focus text-danger-main' : 'bg-warning-focus text-warning-main' }}">{{ ucfirst($incident->severity?->value ?? 'unknown') }}</span></td>
                                <td>{{ \Illuminate\Support\Str::limit($incident->description, 80) }}</td>
                                <td>{{ $incident->escalated ? ($incident->escalatedTo?->name ?: 'Escalated') : 'Open' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
