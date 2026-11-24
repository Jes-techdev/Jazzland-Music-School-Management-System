<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Instrument extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class)
            ->withTimestamps();
    }
}