<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artist extends Model
{
    protected $guarded = [];

    public function socials() : HasOne
    {
        return $this->hasOne(Socials::class);
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
