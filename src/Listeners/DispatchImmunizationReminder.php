<?php

namespace Illimi\Health\Listeners;

use Illimi\Health\Events\ImmunizationDue;
use Illimi\Health\Jobs\SendImmunizationReminderJob;

class DispatchImmunizationReminder
{
    public function handle(ImmunizationDue $event): void
    {
        SendImmunizationReminderJob::dispatch($event->immunization->id);
    }
}
