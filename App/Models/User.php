<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;
                      

class User extends \Core\Model{
    public $errors = [];

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public function save(){

        $this->validate();

        if(empty($this->errors)){

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue();

            $sql = 'INSERT INTO users (username, email, password, activation_hash)
                    VALUES (:username, :email, :password, :activation_hash)';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }

    public function validate(){
        // Username
        if ($this->username == ''){
            $this->errors[] = 'Podaj imię';
        }
         // Email
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false){
            $this->errors[] = 'Niepoprawny email';
        }
        if (static::emailExists($this->email, $this->id ?? null)){
            $this->errors[] = 'Już istnieje konto z tym adresem e-mail ';
        }
        // Password
        if (isset($this->password)){
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

    }

    public static function emailExists($email, $ignore_id = null){
        $user =  static::findByEmail($email);

        if ($user){
            if ($user->id != $ignore_id){
                return true;
            }
        }
        return false;
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

        if ($user && $user->is_active){
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
                $user->sendPasswordResetEmail();
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

        $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);

        Mail::send($this->email, 'Resetowanie hasła', $text, $html);
     }

     public static function findByPasswordReset($token){
        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'SELECT * FROM users
                WHERE password_reset = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user){
            if (strtotime($user->password_reset_expires_at) > time()) {
                return $user;
            }
        }
     }

     public function resetPassword($password){
        $this->password = $password;
        
        $this->validate();


        if (empty($this->errors)){
            $password = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'UPDATE users SET password = :password, password_reset = NULL, password_reset_expires_at = NULL WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':password', $password, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
     }

    public function sendActivationEmail(){
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;

        $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);

        Mail::send($this->email, 'Aktywacja konta', $text, $html);
    }

    public static function activate($value){
        $token = new Token($value);
        $hashed_token = $token->getHash();

        $sql = 'UPDATE users SET is_active = 1, activation_hash = null WHERE activation_hash = :hashed_token';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function updateProfile($data){
        $this->username = $data['username'];
        $this->email = $data['email'];
        
        if ($data['password'] != ''){
            $this->password = $data['password'];
        }

        $this->validate();

        if (empty($this->errors)){

            $sql = 'UPDATE users 
                    SET username = :username, 
                        email = :email';
                        if (isset($this->password)){
                            $sql .= ', password = :password';
                        } 
                  $sql .= "\nWHERE id = :id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            if (isset($this->password)){
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
            }
            return $stmt->execute();
        }
        return false;
    }

    public function setDefaultIncomesCategories(){
        $user =  static::findByEmail($this->email);
        $email = $user->email;

        $sql = "INSERT INTO incomes_category_assigned_to_users (id, user_id, name) 
                SELECT incomes_category_default.id, users.id, incomes_category_default.name 
                FROM incomes_category_default, users WHERE email = '$email'";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();
    }

    public function setDefaultExpensesCategories()
    {
        $user =  static::findByEmail($this->email);
        $email = $user->email;

        $sql = "INSERT INTO expenses_category_assigned_to_users (id, user_id, name) 
                SELECT expenses_category_default.id, users.id, expenses_category_default.name 
                FROM expenses_category_default, users WHERE email = '$email'";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();
    }

    public function setDefaultPaymentMethods()
    {
        $user =  static::findByEmail($this->email);
        $email = $user->email;

        $sql = "INSERT INTO payment_methods_assigned_to_users (id, user_id, name) 
                SELECT payment_methods_default.id, users.id, payment_methods_default.name 
                FROM payment_methods_default, users WHERE email = '$email'";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();
    }
}
