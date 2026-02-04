<?php

namespace App\Classes;

use DateTime;
use DateTimeZone;

class TimeConverter
{
    
    public function GetCurrentEasternTime(){
        $newYork_tz = new DateTimeZone('America/New_York');
        $dateTime = new DateTime('now', $newYork_tz);
        return $dateTime;
    }

    public function GetCurrentCentralTime(){
        $central_tz = new DateTimeZone('America/Chicago');
        $dateTime = new DateTime('now', $central_tz);
        return $dateTime;
    }

    public function ConvertTime($releaseDate){
        $newYork_tz = new DateTimeZone('America/New_York');
        return date_create($releaseDate, $newYork_tz);
    }

    public function ConvertTimeToCentral($timeToConvert){
        $central_tz = new DateTimeZone('America/Chicago');
        return date_create($timeToConvert, $central_tz);
    }

    public function IsHoliday(){
        $currentYear = date("Y");

        $thanksgiving = date('Y-m-d', strtotime("fourth thursday of november $currentYear"));

        return $this->GetCurrentEasternTime > $this->ConvertTime($thanksgiving);
    }
}