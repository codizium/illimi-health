<?php

namespace Illimi\Health\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illimi\Health\Models\Immunization;

class ImmunizationDue
{
    use Dispatchable, SerializesModels;

    public function __construct(public Immunization $immunization)
    {
    }
}
