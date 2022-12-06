<?php

namespace App\Models;

use PDO;

class ExpenseCategory extends Category{
    public $errors = [];

    public function __construct($data = []){
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public function addExpenseCategory(){
        $this->validate();

        if (empty($this->errors)) {
            $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name)
                    VALUES (:user_id, :name)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', strtolower($this->nameCategory), PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }

    public function validate(){
        if ($this->nameCategory == '') {
            $this->errors[] = 'Podaj nazwę kategorii.';
        }
        if (static::categoryExpenseExist($this->nameCategory)) {
            $this->errors[] = 'Już istnieje kategoria z tą nazwą.';
        }
        if (isset($this->nameCategory)) {
            if (strlen($this->nameCategory) < 3 || strlen($this->nameCategory) > 30) {
                $this->errors[] = 'Nazwa powinna zawierać od 3 do 30 znaków.';
            }
        }
    }

    public static function categoryExpenseExist($nameCategory){
        $sql = 'SELECT * FROM expenses_category_assigned_to_users 
                WHERE name = :name AND user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $nameCategory, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function validateNewName(){
        if (static::categoryExpenseExist($this->newNameCategory)) {
            $this->errors[] = 'Już istnieje kategoria z tą nazwą.';
        }
    }

    public function editExpenseCategory($oldCategory){
        $this->validateNewName();

        if (empty($this->errors)) {
            $sql = 'UPDATE expenses_category_assigned_to_users
                    SET name = :name
                    WHERE user_id = :user_id AND name = :oldNameCategory';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', strtolower($this->newNameCategory), PDO::PARAM_STR);
            $stmt->bindValue(':oldNameCategory', $oldCategory, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }

    public static function deleteExpenseCategory($oldCategory){
        $sql = 'DELETE FROM expenses_category_assigned_to_users
                WHERE user_id = :user_id AND name = :oldNameCategory';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':oldNameCategory', $oldCategory, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function deleteExpensesAssignedToDeletedCategory($oldIdCategory){
        $sql = 'DELETE FROM expenses
                WHERE user_id = :user_id AND expense_category_assigned_to_user_id = :oldIdCategory';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':oldIdCategory', $oldIdCategory, PDO::PARAM_INT);

        return $stmt->execute();
    }


    public function setLimitExpenseCategory($oldCategory){
        $sql = 'UPDATE expenses_category_assigned_to_users 
                SET monthly_limit = :limit
                WHERE user_id = :user_id AND name = :oldNameCategory';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
            $stmt->bindValue(':limit', $this->limit, PDO::PARAM_INT);
            $stmt->bindValue(':oldNameCategory', $oldCategory, PDO::PARAM_STR);

            return $stmt->execute();
        
    }

    public static function getLimitExpenseCategory(){
        $sql = 'SELECT * FROM expenses_category_assigned_to_users 
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMonthlySumCategory(){
        $sql = 'SELECT name, date_of_expense, amount
                FROM expenses, expenses_category_assigned_to_users
                WHERE expenses.user_id = :user_id 
                AND expenses.user_id = expenses_category_assigned_to_users.user_id
                AND expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
                ORDER BY date_of_expense ASC, name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
