<?php

namespace App\Models;

use PDO;

class PaymentMethod extends Category
{
    public $errors = [];

    public function __construct($data = []){
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public function addPaymentMethod(){
        $this->validate();

        if (empty($this->errors)) {
            $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name)
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
            $this->errors[] = 'Podaj nazwę metody płatności.';
        }
        if (static::paymentMethodExist($this->nameCategory)) {
            $this->errors[] = 'Już istnieje metoda płatności z tą nazwą.';
        }
        if (isset($this->nameCategory)) {
            if (strlen($this->nameCategory) < 3 || strlen($this->nameCategory) > 30) {
                $this->errors[] = 'Nazwa powinna zawierać od 3 do 30 znaków.';
            }
        }
    }

    public static function paymentMethodExist($nameCategory){
        $sql = 'SELECT * FROM payment_methods_assigned_to_users 
        WHERE name = :name AND user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $nameCategory, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function validateNewName(){
        if (static::paymentMethodExist($this->newNameCategory)) {
            $this->errors[] = 'Już istnieje sposób płatności z tą nazwą.';
        }
    }

    public function editPaymentMethod($oldCategory){
        $this->validateNewName();

        if (empty($this->errors)) {
            $sql = 'UPDATE payment_methods_assigned_to_users
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

    public static function deletePaymentMethod($oldCategory){
        $sql = 'DELETE FROM payment_methods_assigned_to_users
                    WHERE user_id = :user_id AND name = :oldNameCategory';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':oldNameCategory', $oldCategory, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function deleteExpenseAssignedToDeletedPaymentMethod($oldIdCategory){
        $sql = 'DELETE FROM expenses
                    WHERE user_id = :user_id AND payment_method_assigned_to_user_id = :oldIdCategory';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindValue(':oldIdCategory', $oldIdCategory, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
