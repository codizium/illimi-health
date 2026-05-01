<?php

namespace Illimi\Health\Jobs;

use Codizium\Core\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illimi\Health\Models\HealthIncident;
use Illimi\Health\Models\MedicalVisit;
use Illimi\Health\Notifications\IncidentAlertNotification;
use Illimi\Health\Notifications\MedicalVisitAlertNotification;

class SendParentHealthAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $type,
        protected string $recordId
    ) {
    }

    public function handle(): void
    {
        if ($this->type === 'visit') {
            $visit = MedicalVisit::with('student.parents')->find($this->recordId);

            if (!$visit) {
                return;
            }

            foreach ($visit->student?->parents ?? [] as $parent) {
                $parent->notify(new MedicalVisitAlertNotification($visit));
            }

            $visit->forceFill([
                'parent_notified' => true,
                'notified_at' => now(),
            ])->save();

            return;
        }

        $incident = HealthIncident::with('student.parents')->find($this->recordId);

        if (!$incident) {
            return;
        }

        foreach ($incident->student?->parents ?? [] as $parent) {
            $parent->notify(new IncidentAlertNotification($incident));
        }

        $incident->forceFill([
            'parent_notified' => true,
            'notified_at' => now(),
        ])->save();
    }
}
