<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Programme extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ProgrammeSession::class);
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(ProgrammeEnrolment::class);
    }
}