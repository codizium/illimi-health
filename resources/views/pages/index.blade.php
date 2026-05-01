@extends('layouts.app')

@section('content')
    @php
        $modules = [
            ['label' => 'Medical Profiles', 'route' => route('health.profiles'), 'icon' => 'ri-heart-pulse-line', 'count' => $profileCount, 'text' => 'Encrypted health records, allergies, medications, and emergency contact chains.'],
            ['label' => 'Sick Bay Visits', 'route' => route('health.visits'), 'icon' => 'ri-first-aid-kit-line', 'count' => $visitCount, 'text' => 'Log complaints, treatment, and sent-home outcomes with parent notifications.'],
            ['label' => 'Incidents', 'route' => route('health.incidents'), 'icon' => 'ri-alarm-warning-line', 'count' => $incidentCount, 'text' => 'Track incidents, escalations, and management alerts in one workflow.'],
            ['label' => 'Immunizations', 'route' => route('health.immunizations'), 'icon' => 'ri-syringe-line', 'count' => $dueImmunizationsCount, 'text' => 'Monitor vaccine records, upcoming due dates, and reminder activity.'],
        ];
    @endphp

    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Health & Welfare</h1>
            <div>
                <a href="{{ route('home') }}" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Health</span>
            </div>
        </div>
    </div>

    <div class="row gy-4">
        @foreach ($modules as $module)
            <div class="col-xl-3 col-md-6">
                <a href="{{ $module['route'] }}" class="card h-100 text-decoration-none">
                    <div class="card-body p-24">
                        <div class="d-flex align-items-center justify-content-between mb-20">
                            <div class="w-56-px h-56-px rounded-circle bg-danger-50 text-danger-600 d-inline-flex align-items-center justify-content-center text-2xl">
                                <i class="{{ $module['icon'] }}"></i>
                            </div>
                            <span class="badge bg-danger-focus text-danger-main">{{ $module['count'] }}</span>
                        </div>
                        <h6 class="mb-10 text-primary-light">{{ $module['label'] }}</h6>
                        <p class="text-secondary-light mb-20">{{ $module['text'] }}</p>
                        <span class="text-primary-600 fw-semibold d-inline-flex align-items-center gap-1">
                            Open
                            <i class="ri-arrow-right-line"></i>
                        </span>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
