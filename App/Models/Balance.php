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

        $this->getBalanceData();
    }

    protected function getBalanceData(){
        $this->getAllIncomes();
    }

    protected function getAllIncomes(){
        $sql = "SELECT income_category_assigned_to_user_id, amount, date_of_income, income_comment
                FROM incomes
                WHERE user_id = :id AND date_of_income BETWEEN :startDate AND :endDate
                ORDER BY date_of_income ASC, income_category_assigned_to_user_id";
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':startDate', $this->startDate, PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $this->endDate, PDO::PARAM_STR);
        $stmt->execute();


        $this->incomes = $stmt->fetchAll();
    }
    
}