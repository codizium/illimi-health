<?php

namespace Illimi\Health\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illimi\Health\Models\HealthIncident;

class IncidentAlertNotification extends Notification
{
    use Queueable;

    public function __construct(protected HealthIncident $incident)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Student Incident Alert')
            ->line('A health incident has been reported.')
            ->line('Severity: '.($this->incident->severity?->value ?? 'unknown'))
            ->line('Description: '.$this->incident->description);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'incident_id' => $this->incident->id,
            'student_id' => $this->incident->student_id,
            'severity' => $this->incident->severity?->value,
        ];
    }
}
