<?php

namespace Illimi\Health\Listeners;

use Codizium\Core\Models\User;
use Illimi\Health\Events\IncidentEscalated;
use Illimi\Health\Jobs\EscalateIncidentJob;
use Illimi\Health\Notifications\IncidentAlertNotification;

class AlertManagementOnCriticalIncident
{
    public function handle(IncidentEscalated $event): void
    {
        EscalateIncidentJob::dispatch($event->incident->id);

        $roles = config('illimi-health.management_roles', ['admin', 'principal']);

        User::query()
            ->where('organization_id', $event->incident->organization_id)
            ->whereHas('roles', fn ($query) => $query->whereIn('name', $roles))
            ->get()
            ->each(fn (User $user) => $user->notify(new IncidentAlertNotification($event->incident)));
    }
}
