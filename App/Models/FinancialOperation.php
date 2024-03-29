<?php

namespace App\Models;

use PDO;

class FinancialOperation extends \Core\Model{
    public $errors = [];

    public function __construct($financialOperation = []){
        foreach ($financialOperation as $key => $value) {
            $this->$key = $value;
        };
    }
    
    public function saveIncome(){
        $this->validate();

        if (empty($this->errors)){
        $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
                VALUES (:user_id, (SELECT id FROM incomes_category_assigned_to_users 
                WHERE name = :name AND user_id = :user_id ), :amount, :date_of_income, :income_comment)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':name', $this->category, PDO::PARAM_STR);
        $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
        $stmt->bindValue(':date_of_income', $this->date, PDO::PARAM_STR);
        $stmt->bindValue(':income_comment', $this->comment, PDO::PARAM_STR);

        return $stmt->execute();
        }
        return false;
    }

    public function saveExpense(){
        $this->validate();

        if (empty($this->errors)) {
            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
                    VALUES (:user_id, (SELECT id FROM expenses_category_assigned_to_users WHERE name = :nameCategory AND user_id = :user_id ), 
                    (SELECT id FROM payment_methods_assigned_to_users WHERE namePayment = :namePayment AND user_id = :user_id ), :amount, :date_of_expense, :expense_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
            $stmt->bindValue(':nameCategory', $this->category, PDO::PARAM_STR);
            $stmt->bindValue(':namePayment', $this->payment, PDO::PARAM_STR);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_expense', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':expense_comment', $this->comment, PDO::PARAM_STR);
            
            return $stmt->execute();
        }
        return false;
    }

    public function validate(){
        if (isset($this->comment)) {
            if (strlen($this->comment) > 100) {
                $this->errors[] = 'Komentarz nie może mieć więcej niż 100 znaków';
            }
        }
    }
  
}