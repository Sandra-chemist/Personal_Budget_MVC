<?php

namespace App\Models;

use PDO;

class IncomeCategory extends Category
{
    public $errors = [];

    public function __construct($data = []){
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public function addIncomeCategory(){
        $this->validate();

        if (empty($this->errors)) {
            $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name)
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
        if (static::categoryIncomeExist($this->nameCategory)) {
            $this->errors[] = 'Już istnieje kategoria z tą nazwą.';
        }
        if (isset($this->nameCategory)) {
            if (strlen($this->nameCategory) < 3 || strlen($this->nameCategory) > 30) {
                $this->errors[] = 'Nazwa powinna zawierać od 3 do 30 znaków.';
            }
        }
    }

    public static function categoryIncomeExist($nameCategory){
        $sql = 'SELECT * FROM incomes_category_assigned_to_users 
        WHERE name = :name AND user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $nameCategory, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function validateNewName(){
        if ($this->newNameCategory == '') {
            $this->errors[] = 'Podaj nazwę kategorii.';
        }
        if (static::categoryIncomeExist($this->newNameCategory)) {
            $this->errors[] = 'Już istnieje kategoria z tą nazwą.';
        }
        if (isset($this->newNameCategory)) {
            if (strlen($this->newNameCategory) < 3 || strlen($this->newNameCategory) > 30) {
                $this->errors[] = 'Nazwa powinna zawierać od 3 do 30 znaków.';
            }
        }
    }

    public function editIncomeCategory(){
        $this->validateNewName();

        if (empty($this->errors)) {
            $sql = 'UPDATE incomes_category_assigned_to_users
                    SET name = :name
                    WHERE user_id = :user_id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', strtolower($this->newNameCategory), PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }
}
