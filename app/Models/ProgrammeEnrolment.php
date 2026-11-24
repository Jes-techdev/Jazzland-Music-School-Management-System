<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgrammeEnrolment extends Model
{
    protected $fillable = [
        'programme_id',
        'student_id',
        'status',
        'enrolled_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
        ];
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}