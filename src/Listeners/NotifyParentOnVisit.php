<?php

namespace Illimi\Health\Listeners;

use Illimi\Health\Events\MedicalVisitLogged;
use Illimi\Health\Events\StudentSentHome;
use Illimi\Health\Jobs\SendParentHealthAlertJob;

class NotifyParentOnVisit
{
    public function handle(object $event): void
    {
        $visit = $event instanceof StudentSentHome ? $event->visit : $event->visit;

        SendParentHealthAlertJob::dispatch('visit', $visit->id);
    }
}
