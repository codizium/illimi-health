<?php

namespace Illimi\Health\Console;

use Illuminate\Console\Command;
use Illimi\Health\Events\ImmunizationDue;
use Illimi\Health\Jobs\SendImmunizationReminderJob;
use Illimi\Health\Services\ImmunizationService;

class SendImmunizationRemindersCommand extends Command
{
    protected $signature = 'illimi-health:send-immunization-reminders';

    protected $description = 'Dispatch reminders for immunizations that are due soon.';

    public function handle(ImmunizationService $immunizations): int
    {
        $count = 0;

        foreach ($immunizations->due() as $record) {
            event(new ImmunizationDue($record));
            SendImmunizationReminderJob::dispatch($record->id);
            $count++;
        }

        $this->info("Dispatched {$count} immunization reminders.");

        return self::SUCCESS;
    }
}
