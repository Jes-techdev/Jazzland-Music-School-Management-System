<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instructor extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'bio',
        'hire_date',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function instruments(): BelongsToMany
    {
        return $this->belongsToMany(Instrument::class)
            ->withTimestamps();
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(InstructorAvailability::class);
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class)
            ->withTimestamps();
    }
}