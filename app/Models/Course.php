<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = [
        'name',
        'description',
        'lesson_type',
        'duration_minutes',
        'default_capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function instruments(): BelongsToMany
    {
        return $this->belongsToMany(Instrument::class)
            ->withTimestamps();
    }
}
