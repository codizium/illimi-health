<?php

namespace Illimi\Health\Listeners;

use Illimi\Health\Events\IncidentReported;
use Illimi\Health\Jobs\SendParentHealthAlertJob;

class NotifyParentOnIncident
{
    public function handle(IncidentReported $event): void
    {
        SendParentHealthAlertJob::dispatch('incident', $event->incident->id);
    }
}
