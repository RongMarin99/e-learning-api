<?php

use Illuminate\Support\Carbon;
use KhmerDateTime\KhmerDateTime;

if (!function_exists('getShowDate')) {
    function getShowDate($value, $format)
    {
        if ($value) {
            if ($value != "0000-00-00") {
                switch (strtoupper($format)) {
                    case "DD.MM.YYYY":
                        $format = "d.m.Y";
                        break;
                    case "DD-MM-YYYY":
                        $format = "d-m-Y";
                        break;
                    case "DD/MM/YYYY":
                        $format = "d/m/Y";
                        break;
                    case "MM.DD.YYYY":
                        $format = "m.d.Y";
                        break;
                    case "MM-DD-YYYY":
                        $format = "m-d-Y";
                        break;
                    case "MM/DD/YYYY":
                        $format = "m/d/Y";
                        break;
                    case "YYYY/MM/DD":
                        $format = "Y/m/d";
                        break;
                    default:
                        $format = "d.m.Y";
                        break;
                }
                return Carbon::parse($value)->format($format);
            } else {
                return "---";
            }
        } else {
            return "---";
        }
    }
}

if(!function_exists("getDateInEngineisEnglish")){
    function getDateInEngineisEnglish($date){

        date_default_timezone_set('Asia/Phnom_Penh');
        $date = date("D M j Y G:i:s");
        return $date;
        
    }
}

if(!function_exists("CounTimeToget")){
    function CounTimeToget(){

        date_default_timezone_set('Asia/Phnom_Penh');
        $date = date("Y-m-d  H:m:s");
        
        return $date;
        
    }
}