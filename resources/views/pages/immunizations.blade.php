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
                    <tbody>
                        @foreach ($immunizations as $item)
                            <tr>
                                <td>{{ $item->student?->full_name ?? 'Unknown student' }}</td>
                                <td>{{ $item->vaccine_name }}</td>
                                <td>{{ $item->dose_number }}</td>
                                <td>{{ $item->due_date?->format('d M Y') ?: '—' }}</td>
                                <td><span class="badge bg-primary-50 text-primary-600">{{ ucfirst($item->status?->value ?? 'unknown') }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
