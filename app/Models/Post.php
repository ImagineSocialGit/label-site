<?php

namespace App\Models;

use App\Classes\TimeConverter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $guarded = [];

    public function artist() : BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function isPublished(){
        $timeConverter = new TimeConverter();
        $currentTime = $timeConverter->GetCurrentEasternTime();
        $publishTime = $timeConverter->ConvertTime($this->publish_date);

        return $publishTime < $currentTime;
    }

    public function isLive(): bool
    {
        return static::where('slug', $this->slug)
                ->where('env', 'production')
                ->where('external_site_id', null)
                ->exists();
    }

    public function scopeStaging($query)
    {
        return $query->where('env', 'staging');
    }
}
