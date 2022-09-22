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
    public function prepare(){
        //Active period selector
        if (!isset($this->activeTimePeriod)) {
            $this->activeTimePeriod = 'currentMonth';
        }

        //Set proper startDate & endDate depending on input
        switch ($this->activeTimePeriod) {
            default:
            case 'currentMonth':
                $this->startDate = date('Y-m-01');
                $this->endDate = date("Y-m-d");
                break;

            case 'lastMonth':
                $this->startDate = date('Y-m-01', strtotime('last month'));
                $this->endDate = date('Y-m-t', strtotime('last month'));
                break;

            case 'currentYear':
                $this->startDate = date('Y-01-01');
                $this->endDate = date("Y-m-d");
                break;

            case 'customPeriod':
                $this->startDate = $_POST['postStartDate'];
                $this->endDate =  $_POST['postEndDate'];

                //Validate input date
                if (!$this->validateDate($this->endDate) || !$this->validateDate($this->startDate)) {
                    return false;
                }
                break;
        }
        return true;
    }
}