<?php

namespace Illimi\Health\Jobs;

use Codizium\Core\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illimi\Health\Models\HealthIncident;
use Illimi\Health\Notifications\IncidentAlertNotification;

class EscalateIncidentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected string $incidentId)
    {
    }

    public function handle(): void
    {
        $incident = HealthIncident::find($this->incidentId);

        if (!$incident || !$incident->escalated_to) {
            return;
        }

        $recipient = User::find($incident->escalated_to);

        if ($recipient) {
            $recipient->notify(new IncidentAlertNotification($incident));
        }
    }
}
