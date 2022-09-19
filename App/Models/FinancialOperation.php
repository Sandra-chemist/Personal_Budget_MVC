<?php

namespace App\Models;

use PDO;

class FinancialOperation extends \Core\Model{
    public function __construct($financialOperation = [])
    {
        foreach ($financialOperation as $key => $value) {
            $this->$key = $value;
        };
    }
    public function saveIncome(){

        $income_category_id = 1;
        $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
                    VALUES (:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);


        $amount = '';

        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':income_category_assigned_to_user_id', $income_category_id, PDO::PARAM_STR);
        $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
        $stmt->bindValue(':date_of_income', $this->date, PDO::PARAM_STR);
        $stmt->bindValue(':income_comment', $this->comment, PDO::PARAM_STR);

        return $stmt->execute();
    }
}