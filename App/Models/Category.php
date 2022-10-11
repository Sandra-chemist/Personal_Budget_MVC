<?php

namespace App\Models;

use PDO;

class Category extends \core\Model{
    public function __construct($category = []){
        foreach ($category as $key => $value) {
            $this->$key = $value;
        };
    }
    
    public static function getLoggedUserIncomeCategories(){
        $sql = "SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    } 

    
}