<?php

namespace Illimi\Health\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illimi\Health\Models\MedicalVisit;

class MedicalVisitAlertNotification extends Notification
{
    use Queueable;

    public function __construct(protected MedicalVisit $visit)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Student Health Visit Alert')
            ->line('A student health visit has been recorded.')
            ->line('Complaint: '.$this->visit->complaint)
            ->line('Outcome: '.($this->visit->outcome?->value ?? 'pending'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'visit_id' => $this->visit->id,
            'student_id' => $this->visit->student_id,
            'outcome' => $this->visit->outcome?->value,
        ];
    }
}
