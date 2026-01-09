<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Label extends Model
{
    protected $guarded = [];

    public function artists() : HasMany
    {
        return $this->hasMany(Artist::class);
    }
}
