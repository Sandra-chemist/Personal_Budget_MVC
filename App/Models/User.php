<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model{
    /**
     *  Error messages
     * 
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values
     *
     * @return void
     */
    public function __construct($data){
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Save the user model with the current property values
     *
     * @return void
     */
    public function save(){

        $this->validate();

        if(empty($this->errors)){

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (username, email, password)
                    VALUES (:username, :email, :password)';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }

    /**
     * Validate current property values, adding validation error message to the errors array property
     * 
     * @return void
     */
    public function validate(){
        // Username
        if ($this->username == ''){
            $this->errors[] = 'Podaj imię';
        }
         // Email
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false){
            $this->errors[] = 'Niepoprawny email';
        }
        // Password
        if ($this->password !=  $this->password_confirmation) {
            $this->errors[] = 'Niepoprawne hasło';
        }

        if (strlen($this->password) < 8){
            $this->errors[] = 'Hasło powinno zawierać co najmniej 8 znaków';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors[] = 'Hasło powinno zawierać co najmniej jedną literę';
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors[] = 'Hasło powinno zawierać co najmniej jedną cyfrę';
        }

    }

}
