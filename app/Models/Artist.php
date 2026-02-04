<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artist extends Model
{
    protected $guarded = [];

    public function snippedAbout() {
        
        $snip = implode(' ', array_slice(explode(' ', str_replace('-', ' ', $this->about)), 0, 18));

        return $snip;
    }

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

    public function music() : HasMany
    {
        return $this->hasMany(Music::class);
    }

    public function videos() : HasMany
    {
        return $this->hasMany(Video::class);
    }
}
