<?php

namespace Illimi\Health\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Models\User;
use Illimi\Health\Enums\VisitOutcomeEnum;
use Illimi\Students\Models\Student;

class MedicalVisit extends BaseModel
{
    protected $table = 'illimi_medical_visits';

    protected $fillable = [
        'organization_id',
        'student_id',
        'attended_by',
        'visit_date',
        'time_in',
        'time_out',
        'complaint',
        'diagnosis',
        'treatment',
        'medication_given',
        'outcome',
        'parent_notified',
        'notified_at',
        'follow_up_required',
        'follow_up_date',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'student_id' => 'string',
        'attended_by' => 'string',
        'visit_date' => 'date',
        'medication_given' => 'array',
        'outcome' => VisitOutcomeEnum::class,
        'parent_notified' => 'boolean',
        'notified_at' => 'datetime',
        'follow_up_required' => 'boolean',
        'follow_up_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function attendee()
    {
        return $this->belongsTo(User::class, 'attended_by');
    }
}
