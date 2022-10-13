<?php

namespace App\Models;

use PDO;

class IncomeCategory extends Category{

    public function editIncomeCategory(){

        $this->validateDate();

        if (self::getLoggedUserIncomeCategories($this->categoryNewName)) {
            $this->validationErrors['name'] = "Taka nazwa kategorii już istnieje.";
        }

        if (empty($this->validationErrors)){
            $this->incomeCategoryNewId = $this->getIncomeCategoryId();
        }
    }

    protected function getIncomeCategoryId(){
        $sql = 'SELECT id
                FROM income_category_assigned_to_users
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $this->categoryNewName, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }
}