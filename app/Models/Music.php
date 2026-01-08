<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Music extends Model
{
    protected $guarded = [];

    public function artist() : BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}
