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
}
