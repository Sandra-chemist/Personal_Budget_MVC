<?php

namespace App\Models;

use \App\Token;
use PDO;
use \App\Mail;
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
    public function __construct($data = []){
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
        if (static::emailExists($this->email)){
            $this->errors[] = 'Już istnieje konto z tym adresem e-mail ';
        }
        // Password
        if (strlen($this->password) < 6){
            $this->errors[] = 'Hasło powinno zawierać co najmniej 6 znaków';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors[] = 'Hasło powinno zawierać co najmniej jedną literę';
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors[] = 'Hasło powinno zawierać co najmniej jedną cyfrę';
        }

    }

    /**
     *  See if a user record already exist with the specified email
     * 
     * @param string $email email address to search for
     * 
     * @return boolean True if a record already exists with the specified email, false otherwise 
     */
    public static function emailExists($email){
        return static::findByEmail($email) !== false;
    }

    public static function findByEmail($email){
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function authenticate($email, $password){
        $user = static::findByEmail($email);

        if ($user){
            if(password_verify($password, $user->password)){
                return $user;
            }
        }
        return false;
    }

    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public function rememberLogin(){

        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function sendPasswordReset($email){
        $user = static::findByEmail($email);

        if ($user){
             if ($user->startPasswordReset()){
                $user->sendPasswordResetEmail
             }
        }
    }

    protected function startPasswordReset(){
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->password_reset_token = $token->getValue();

        $expiry_timestamp = time() + 60 * 60 * 2;

        $sql = 'UPDATE users SET password_reset = :token_hash, password_reset_expires_at = :expires_at WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
     }

     protected function sendPasswordResetEmail(){
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->password_reset_token;

        $text = "Proszę kliknąć w link, żeby zresetować hasło: $url";
        $html = "Proszę kliknąć <a href=\"$url\">Tutaj</a>, żeby zresetować hasło.";

        Mail::send($this->email, 'Resetowanie_hasła', $text, $html);

     }
}
