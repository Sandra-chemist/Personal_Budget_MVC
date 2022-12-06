<?php

namespace App;

class Date{
    public static function getCurrentDate(){
        return date("Y-m-d");
    }

    public static function getCurrentMonthStartDate(){
        return date("Y-m-01");
    }

    public static function getCurrentMonthEndDate(){
        return date("Y-m-t");
    }

    public static function getPreviousMonthStartDate(){
        return date("Y-m-01", strtotime("first day of previous month"));
    }

    public static function getPreviousMonthEndDate(){
        return date("Y-m-t", strtotime("last day of previous month"));
    }

    public static function getCurrentYearStartDate(){
        return date("Y-01-01");
    }

    public static function getCurrentYearEndDate(){
        return date("Y-12-31");
    }


}