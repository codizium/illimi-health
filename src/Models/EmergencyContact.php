<?php

namespace Illimi\Health\Models;

use Codizium\Core\Models\BaseModel;
use Illimi\Students\Models\Student;

class EmergencyContact extends BaseModel
{
    protected $table = 'illimi_emergency_contacts';

    protected $fillable = [
        'organization_id',
        'student_id',
        'name',
        'relationship',
        'phone',
        'alternate_phone',
        'priority',
        'is_primary',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'student_id' => 'string',
        'priority' => 'integer',
        'is_primary' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
