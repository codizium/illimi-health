<?php

namespace Illimi\Health;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illimi\Health\Console\SendImmunizationRemindersCommand;
use Illimi\Health\Events\ImmunizationDue;
use Illimi\Health\Events\IncidentEscalated;
use Illimi\Health\Events\IncidentReported;
use Illimi\Health\Events\MedicalVisitLogged;
use Illimi\Health\Events\StudentSentHome;
use Illimi\Health\Listeners\AlertManagementOnCriticalIncident;
use Illimi\Health\Listeners\DispatchImmunizationReminder;
use Illimi\Health\Listeners\NotifyParentOnIncident;
use Illimi\Health\Listeners\NotifyParentOnVisit;
use Illimi\Health\Models\HealthIncident;
use Illimi\Health\Models\Immunization;
use Illimi\Health\Models\MedicalProfile;
use Illimi\Health\Models\MedicalVisit;
use Illimi\Health\Policies\HealthPolicy;
use Illimi\Health\Services\ImmunizationService;
use Illimi\Health\Services\IncidentService;
use Illimi\Health\Services\MedicalProfileService;
use Illimi\Health\Services\MedicalVisitService;

class HealthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/illimi-health.php', 'illimi-health');

        $this->app->singleton('illimi-health', function () {
            return new IllimiHealth();
        });

        $this->app->singleton(MedicalProfileService::class);
        $this->app->singleton(MedicalVisitService::class);
        $this->app->singleton(IncidentService::class);
        $this->app->singleton(ImmunizationService::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'illimi-health');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'illimi-health');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/illimi-health.php' => config_path('illimi-health.php'),
            ], 'illimi-health-config');

            $this->commands([
                SendImmunizationRemindersCommand::class,
            ]);
        }

        Event::listen(MedicalVisitLogged::class, NotifyParentOnVisit::class);
        Event::listen(StudentSentHome::class, NotifyParentOnVisit::class);
        Event::listen(IncidentReported::class, NotifyParentOnIncident::class);
        Event::listen(IncidentEscalated::class, AlertManagementOnCriticalIncident::class);
        Event::listen(ImmunizationDue::class, DispatchImmunizationReminder::class);

        Gate::policy(MedicalProfile::class, HealthPolicy::class);
        Gate::policy(MedicalVisit::class, HealthPolicy::class);
        Gate::policy(HealthIncident::class, HealthPolicy::class);
        Gate::policy(Immunization::class, HealthPolicy::class);

        $this->registerMenu();
    }

    protected function registerMenu()
    {
        if (class_exists(\Illimi\IllimiCore\Facades\IllimiCore::class)) {
            $nav = \Illimi\IllimiCore\Facades\IllimiCore::navigation();

            $nav->register('health', [
                'label' => 'Health',
                'icon' => 'ri-heart-pulse-line',
                'category' => 'operations',
                'priority' => 90,
                'roles' => ['admin', 'super-admin'],
                'children' => [
                    ['label' => 'Medical Profiles', 'route' => 'health.profiles'],
                    ['label' => 'Visits Log', 'route' => 'health.visits'],
                    ['label' => 'Incidents', 'route' => 'health.incidents'],
                    ['label' => 'Immunizations', 'route' => 'health.immunizations'],
                ],
            ]);
        }
    }
}
