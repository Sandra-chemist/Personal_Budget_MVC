<?php


namespace App\Models;


use PDO;

class IncomeCategory extends Category{
    public $errors = [];

    public function __construct($data = []){
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
    public function validate(){
        if ($this->nameCategory== '') {
            $this->errors[] = 'Podaj nazwę kategorii.';
        }

        if (isset($this->nameCategory)) {
            if (strlen($this->nameCategory) < 3 || strlen($this->nameCategory) > 30 ) {
                $this->errors[] = 'Nazwa powinna zawierać od 3 do 30 znaków.';
            }
        }
    }

    public function addIncomeCategory(){
        $this->validate();

        if (empty($this->errors)) {
            $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name)
                    VALUES (:user_id, :name)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $this->nameCategory, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }
}