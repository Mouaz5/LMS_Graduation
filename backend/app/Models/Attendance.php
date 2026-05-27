<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'student_user_id',
        'classroom_id',
        'date',
        'status',
        'schedule_slot_id',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function isPresent(): bool  { return $this->status === 'present'; }
    public function isAbsent(): bool   { return $this->status === 'absent'; }
    public function isLate(): bool     { return $this->status === 'late'; }
    public function isExcused(): bool  { return $this->status === 'excused'; }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function scheduleSlot(): BelongsTo
    {
        return $this->belongsTo(ScheduleSlot::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function justification(): HasOne
    {
        return $this->hasOne(AbsenceJustification::class);
    }
}
