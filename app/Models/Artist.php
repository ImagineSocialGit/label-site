<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Artist extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    // Cast boolean fields for convenience
    protected $casts = [
        'music_requires_refresh' => 'boolean',
        'posts_requires_refresh' => 'boolean',
        'videos_requires_refresh' => 'boolean',
        'design_requires_refresh' => 'boolean',
    ];

    // ------------------------------
    // Accessors & Utility Methods
    // ------------------------------

    /**
     * Snippet of 'about' field (first N words)
     */
    public function snippedAbout(int $wordCount = 18): string
    {
        if (empty($this->about)) {
            return '';
        }

        $words = preg_split('/\s+|-/', $this->about); // split by space or dash
        $snip = implode(' ', array_slice($words, 0, $wordCount));

        return $snip;
    }

    /**
     * Accessor for convenience in Blade: $artist->snipped_about
     */
    public function getSnippedAboutAttribute(): string
    {
        return $this->snippedAbout();
    }

    /**
     * Returns true if any refresh flags are set
     */
    public function requiresRefresh(): bool
    {
        return in_array(true, $this->refreshFlags(), true);
    }

    /**
     * Array of refresh flags for each type
     */
    public function refreshFlags(): array
    {
        return [
            'music' => (bool) $this->music_requires_refresh,
            'posts' => (bool) $this->posts_requires_refresh,
            'videos' => (bool) $this->videos_requires_refresh,
            'design' => (bool) $this->design_requires_refresh,
        ];
    }

    // ------------------------------
    // Relationships
    // ------------------------------

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function pageStyles(?string $env = null): HasMany
    {
        $env = $env ?? config('app.env');

        return $this->hasMany(PageStyle::class)->where('env', $env);
    }

    public function pageStyleForDevice(string $device, ?string $env = null): HasOne
    {
        $env = $env ?? config('app.env');

        return $this->hasOne(PageStyle::class)
                    ->where('device_type', $device)
                    ->where('env', $env);
    }

    public function socials(): HasMany
    {
        return $this->hasMany(Socials::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function stagingPosts(): HasMany
    {
        return $this->posts()->staging();
    }

    public function music(): HasMany
    {
        return $this->hasMany(Music::class);
    }

    public function stagingMusic(): HasMany
    {
        return $this->music()->staging();
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function stagingVideos(): HasMany
    {
        return $this->videos()->staging();
    }
}