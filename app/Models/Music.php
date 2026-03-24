<?php

namespace App\Models;

use App\Classes\TimeConverter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Music extends Model
{
    protected $guarded = [];

    public function artist() : BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function isReleased(){
        $timeConverter = new TimeConverter();
        $currentTime = $timeConverter->GetCurrentEasternTime();
        $releaseTime = $timeConverter->ConvertTime($this->release_date);

        $display = $releaseTime < $currentTime;

        if ($display && $this->is_holiday_release){
            $display = $timeConverter->IsHoliday();
        }

        return $display;
    }

    public function isPresave(){
        $timeConverter = new TimeConverter();
        
        $currentTime = $timeConverter->GetCurrentEasternTime();
        $presaveTime = $timeConverter->ConvertTime($this->presave_date);

        $display = $presaveTime < $currentTime;

        if ($display && $this->is_holiday_release){
            $display = $timeConverter->IsHoliday();
        }

        return $display;
    }

    public function scopeStaging($query)
    {
        return $query->where('env', 'staging');
    }
}
