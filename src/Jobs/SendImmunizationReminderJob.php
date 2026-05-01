<?php

namespace Illimi\Health\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illimi\Health\Models\Immunization;

class SendImmunizationReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected string $immunizationId)
    {
    }

    public function handle(): void
    {
        $immunization = Immunization::with('student.parents')->find($this->immunizationId);

        if (!$immunization) {
            return;
        }

        foreach ($immunization->student?->parents ?? [] as $parent) {
            $parent->notify(new class($immunization) extends \Illuminate\Notifications\Notification {
                use \Illuminate\Bus\Queueable;

                public function __construct(protected \Illimi\Health\Models\Immunization $immunization)
                {
                }

                public function via(object $notifiable): array
                {
                    return ['mail', 'database'];
                }

                public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
                {
                    return (new \Illuminate\Notifications\Messages\MailMessage())
                        ->subject('Immunization Due Reminder')
                        ->line('An immunization is due soon for your student.')
                        ->line('Vaccine: '.$this->immunization->vaccine_name)
                        ->line('Due date: '.($this->immunization->due_date?->format('Y-m-d') ?? 'n/a'));
                }

                public function toArray(object $notifiable): array
                {
                    return [
                        'immunization_id' => $this->immunization->id,
                        'student_id' => $this->immunization->student_id,
                        'due_date' => $this->immunization->due_date?->format('Y-m-d'),
                    ];
                }
            });
        }
    }
}
