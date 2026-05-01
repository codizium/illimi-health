<?php

namespace Illimi\Health\Models;

use Codizium\Core\Models\BaseModel;
use Codizium\Core\Models\User;
use Illimi\Health\Enums\IncidentSeverityEnum;
use Illimi\Students\Models\Student;

class HealthIncident extends BaseModel
{
    protected $table = 'illimi_health_incidents';

    protected $fillable = [
        'organization_id',
        'student_id',
        'reported_by',
        'incident_date',
        'description',
        'severity',
        'location',
        'witnesses',
        'action_taken',
        'escalated',
        'escalated_to',
        'escalated_at',
        'parent_notified',
        'notified_at',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'student_id' => 'string',
        'reported_by' => 'string',
        'escalated_to' => 'string',
        'incident_date' => 'date',
        'severity' => IncidentSeverityEnum::class,
        'witnesses' => 'array',
        'escalated' => 'boolean',
        'escalated_at' => 'datetime',
        'parent_notified' => 'boolean',
        'notified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function escalatedTo()
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }
}
