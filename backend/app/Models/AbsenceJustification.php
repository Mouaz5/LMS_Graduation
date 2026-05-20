<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsenceJustification extends Model
{
    protected $fillable = [
        'attendance_id',
        'reason',
        'submitted_by',
        'document_url',
        'status',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
