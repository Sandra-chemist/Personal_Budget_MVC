<?php

namespace App\Models;

use PDO;
use \App\Date;

class Balance extends \Core\Model{
    public function __construct($balance = []){
        foreach ($balance as $key => $value) {
            $this->$key = $value;
        };
    }
    public function getCurrentMonthData(){
        $this->startDate = Date::getCurrentMonthStartDate();
        $this->endDate = Date::getCurrentMonthEndDate();

        //$this->getBalanceData();
    }
    public function getPreviousMonthData()
    {
        $this->startDate = Date::getPreviousMonthStartDate();
        $this->endDate = Date::getPreviousMonthEndDate();

        //$this->getBalanceData();
    }
    public function getCurrentYearData()
    {
        $this->startDate = Date::getCurrentYearStartDate();
        $this->endDate = Date::getCurrentYearEndDate();

        //$this->getBalanceData();
    }
    
}