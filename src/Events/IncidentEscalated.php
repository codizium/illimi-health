<?php

namespace Illimi\Health\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illimi\Health\Models\HealthIncident;

class IncidentEscalated
{
    use Dispatchable, SerializesModels;

    public function __construct(public HealthIncident $incident)
    {
    }
}
