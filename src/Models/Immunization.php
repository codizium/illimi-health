<?php

namespace Illimi\Health\Models;

use Codizium\Core\Models\BaseModel;
use Illimi\Health\Enums\ImmunizationStatusEnum;
use Illimi\Students\Models\Student;

class Immunization extends BaseModel
{
    protected $table = 'illimi_immunizations';

    protected $fillable = [
        'organization_id',
        'student_id',
        'vaccine_name',
        'dose_number',
        'date_given',
        'due_date',
        'administered_by',
        'batch_number',
        'status',
        'notes',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'student_id' => 'string',
        'dose_number' => 'integer',
        'date_given' => 'date',
        'due_date' => 'date',
        'status' => ImmunizationStatusEnum::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
