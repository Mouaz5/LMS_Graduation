<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolCalendar extends Model
{
    protected $table = 'school_calendar';

    protected $fillable = ['school_id', 'date', 'type', 'description'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
