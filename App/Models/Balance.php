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

    public function getCustomPeriodData(){
        if ($this->startDate > $this->endDate) {

            $endDate = $this->startDate;
            $this->startDate = $this->endDate;
            $this->endDate = $endDate;
        }

        $this->getBalanceData();
    }
    
    public function getBalanceData(){
        $this->getAllExpenses();
        $this->getAllIncomes();
        $this->groupedExpenses();
        $this->groupedIncomes();
        $this->sumIncomes();
        $this->sumExpenses();
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

    protected function groupedIncomes(){
        $sqlIncome = 'SELECT name, SUM(amount) AS incomeSum FROM incomes, incomes_category_assigned_to_users
                WHERE incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
                AND incomes.user_id = :user_id AND incomes.user_id = incomes_category_assigned_to_users.user_id 
                AND incomes.date_of_income BETWEEN :startDate AND :endDate
                GROUP BY income_category_assigned_to_user_id ORDER BY incomeSum DESC';

        $db = static::getDB();
        $stmt = $db->prepare($sqlIncome);
        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':startDate', $this->startDate, PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $this->endDate, PDO::PARAM_STR);
        $stmt->execute();

        $this->sumGroupedIncomes = $stmt->fetchAll();
    }

    protected function groupedExpenses(){
        $sql = 'SELECT name, SUM(amount) AS expenseSum FROM expenses, expenses_category_assigned_to_users
                WHERE expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
                AND expenses.user_id = :user_id AND expenses.user_id = expenses_category_assigned_to_users.user_id 
                AND expenses.date_of_expense BETWEEN :startDate AND :endDate
                GROUP BY expense_category_assigned_to_user_id ORDER BY expenseSum DESC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':startDate', $this->startDate, PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $this->endDate, PDO::PARAM_STR);
        $stmt->execute();

        $this->sumGroupedExpenses = $stmt->fetchAll();
    }

    protected function sumIncomes(){
        $sql = 'SELECT amount, SUM(amount) AS totalIncomeSum FROM incomes, incomes_category_assigned_to_users
                WHERE incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
                AND incomes.user_id = :user_id AND incomes.user_id = incomes_category_assigned_to_users.user_id 
                AND incomes.date_of_income BETWEEN :startDate AND :endDate
                ORDER BY totalIncomeSum DESC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':startDate', $this->startDate, PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $this->endDate, PDO::PARAM_STR);
        $stmt->execute();

        $this->sumIncomes = $stmt->fetchAll();
    }

    protected function sumExpenses(){
        $sql = 'SELECT amount, SUM(amount) AS totalExpenseSum FROM expenses, expenses_category_assigned_to_users
                WHERE expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
                AND expenses.user_id = :user_id AND expenses.user_id = expenses_category_assigned_to_users.user_id 
                AND expenses.date_of_expense BETWEEN :startDate AND :endDate
                ORDER BY totalExpenseSum DESC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':startDate', $this->startDate, PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $this->endDate, PDO::PARAM_STR);
        $stmt->execute();

        $this->sumExpenses = $stmt->fetchAll();
}
}