<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $guarded = [];

    public function artist() : BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function scopeStaging($query)
    {
        return $query->where('env', 'staging');
    }
}
