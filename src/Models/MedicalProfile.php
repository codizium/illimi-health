<?php

namespace Illimi\Health\Models;

use Codizium\Core\Models\BaseModel;
use Illimi\Students\Models\Student;

class MedicalProfile extends BaseModel
{
    protected $table = 'illimi_medical_profiles';

    protected $fillable = [
        'organization_id',
        'student_id',
        'blood_group',
        'genotype',
        'allergies',
        'chronic_conditions',
        'disabilities',
        'current_medications',
        'doctor_name',
        'doctor_phone',
        'health_insurance',
        'notes',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'student_id' => 'string',
        'blood_group' => 'encrypted',
        'genotype' => 'encrypted',
        'allergies' => 'encrypted:array',
        'chronic_conditions' => 'encrypted:array',
        'disabilities' => 'encrypted:array',
        'current_medications' => 'encrypted:array',
        'doctor_name' => 'encrypted',
        'doctor_phone' => 'encrypted',
        'health_insurance' => 'encrypted',
        'notes' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class, 'student_id', 'student_id')->orderBy('priority');
    }
}
