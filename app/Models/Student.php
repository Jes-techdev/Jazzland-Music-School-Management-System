<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\ProgrammeEnrolment;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(
            Guardian::class,
            'student_guardian',
            'student_id',
            'guardian_id'
        )
            ->withPivot('relationship_type', 'is_primary')
            ->withTimestamps();
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class)
            ->withTimestamps();
    }

    public function programmeEnrolments()
    {
        return $this->hasMany(ProgrammeEnrolment::class);
    }
}
