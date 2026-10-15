<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_holiday' => 'boolean',
        'is_featured' => 'boolean',
        'from_api' => 'boolean',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function scopeStaging($query)
    {
        return $query->where('env', 'staging');
    }
}