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

        $this->getBalanceData();
    }

    public function getCurrentYearData(){
        $this->startDate = Date::getCurrentYearStartDate();
        $this->endDate = Date::getCurrentYearEndDate();

        $this->getBalanceData();
    }

    public function getBalanceData(){
        $this->getAllExpenses();
        $this->getAllIncomes();
    }

    protected function getAllIncomes(){
        $sql = 'SELECT name, date_of_income, amount, income_comment
                FROM incomes, incomes_category_assigned_to_users WHERE incomes.user_id = :user_id 
                AND incomes.user_id = incomes_category_assigned_to_users.user_id
                AND incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id 
                AND date_of_income BETWEEN :startDate AND :endDate
                ORDER BY date_of_income ASC, name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':startDate', $this->startDate, PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $this->endDate, PDO::PARAM_STR);
        $stmt->execute();

        $this->detailedIncomes = $stmt->fetchAll();
    }

    protected function getAllExpenses(){
        $sql = 'SELECT name, date_of_expense, amount, expense_comment
                FROM expenses, expenses_category_assigned_to_users WHERE expenses.user_id = :user_id 
                AND expenses.user_id = expenses_category_assigned_to_users.user_id
                AND expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id 
                AND date_of_expense BETWEEN :startDate AND :endDate
                ORDER BY date_of_expense ASC, name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':startDate', $this->startDate, PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $this->endDate, PDO::PARAM_STR);
        $stmt->execute();

        $this->detailedExpenses = $stmt->fetchAll();
    }
}