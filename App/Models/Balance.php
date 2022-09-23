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

    public function getPreviousMonthData(){
        $this->startDate = Date::getPreviousMonthStartDate();
        $this->endDate = Date::getPreviousMonthEndDate();

        //$this->getBalanceData();
    }

    public function getCurrentYearData(){
        $this->startDate = Date::getCurrentYearStartDate();
        $this->endDate = Date::getCurrentYearEndDate();

        //$this->getBalanceData();
    }

    public function getBalanceData(){
        $this->getAllExpenses();
    }

    protected function getAllExpenses(){
        $sql = 'SELECT expense_category_assigned_to_user_id, date_of_expense, amount, expense_comment
                FROM expenses WHERE user_id = :user_id AND date_of_expense BETWEEN :startDate AND :endDate
                ORDER BY date_of_expense ASC, expense_category_assigned_to_user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':startDate', $this->startDate, PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $this->endDate, PDO::PARAM_STR);
        $stmt->execute();

        $this->detailedExpenses = $stmt->fetchAll();
    }
}