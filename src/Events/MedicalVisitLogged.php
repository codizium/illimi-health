<?php

namespace Illimi\Health\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illimi\Health\Models\MedicalVisit;

class MedicalVisitLogged
{
    use Dispatchable, SerializesModels;

    public function __construct(public MedicalVisit $visit)
    {
    }
}
