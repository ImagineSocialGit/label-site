<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artist extends Model
{
    protected $guarded = [];

    public function label() : BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function socials() : HasMany
    {
        return $this->hasMany(Socials::class);
    }

    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function music() : HasOne
    {
        return $this->hasOne(Music::class);
    }

    public function videos() : HasOne
    {
        return $this->hasOne(Video::class);
    }
}
