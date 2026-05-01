<?php

namespace Illimi\Health\Traits;

use Illimi\Health\Models\MedicalProfile;

trait HasMedicalProfile
{
    public function medicalProfile()
    {
        return $this->hasOne(MedicalProfile::class, 'student_id', 'id');
    }
}
