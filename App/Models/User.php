<?php

namespace App\Models;

use PDO;
use \App\Mail;
use \App\Token;
use \Core\View;

class User extends \Core\Model
{
    public $errors = [];

    public function __construct($data = []){

         foreach($data as $key => $value){
                $this->$key = $value; 
        }
    }

    public function save(){

        $this->validate();

        if(empty($this->errors)){

                $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

                $token = new Token();
                $hashed_token = $token->getHash();
                $this->activation_token = $token->getValue();

                $sql = 'INSERT INTO users (username, password, email, activation_hash)
                        VALUES (:username, :password, :email, :activation_hash)';

                $db = static::getDB();
                $stmt = $db->prepare($sql);

                $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
                $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
                $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
                $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);

                return $stmt->execute();
        }
        return false;
    }

    public function validate(){

        if($this->username == ''){
            $this->errors[] = 'Imię jest wymagane';
        }

        if(filter_var($this->email, FILTER_VALIDATE_EMAIL) === false){
            $this->errors[] = 'Nieprawidłowy email';
        }

        if(static::emailExists($this->email, $this->id ?? null)){
            $this->errors[] = 'Wprowadzony email już istnieje';
        }

        if(strlen($this->password) < 6){
            $this->errors[] = 'Hasło powinno zawierać conajmniej 6 znaków';
        }

        if(preg_match('/.*[a-z]+.*/i', $this->password) == 0){
            $this->errors[] = 'Hasło wymaga conajmniej jednej litery';
        }

        if(preg_match('/.*\d+.*/i', $this->password) == 0){
            $this->errors[] = 'Hasło wyamaga conajmniej jedną cyfrę';
        }
    }

     public static function emailExists($email, $ignore_id = null){

      $user = static::findByEmail($email);

      if($user){
        if($user->id != $ignore_id){
            return true;
        }
      }
      return false;
    }

    public static function findByEmail($email){

        $sql = 'SELECT *
                FROM users
                WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function authenticate($email, $password){

        $user = static::findByEmail($email);

        if($user && $user->is_active){
            if(password_verify($password, $user->password)){
                return $user;
            }
        }
        return false;
    }

    public static function findByID($id){

        $sql = 'SELECT *
                FROM users 
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode (PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function sendPasswordReset($email){

        $user = static::findByEmail($email);

        if($user){
            
            if($user->startPasswordReset()){

               $user->sendPasswordResetEmail();
            }
        }
    }

    protected function startPasswordReset(){

        $token = new Token();

        $hashed_token = $token->getHash();

        $this->password_reset_token = $token->getValue();

        $expiry_timestamp = time() + 60 * 60 * 2;

        $sql = 'UPDATE users
                SET password_reset_hash = :token_hash,
                    password_reset_expires_at = :expires_at
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    protected function sendPasswordResetEmail(){

        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->password_reset_token;
    
        $text = View::getTemplate('Password/reset_email.html', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);
    
        Mail::send($this->email, 'Reset hasła', $text, $html);
    }

    public static function findByPasswordReset($token){

        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'SELECT *
                FROM users
                WHERE password_reset_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $user = $stmt->fetch();

        if($user){
            if(strtotime($user->password_reset_expires_at) > time()){
                return $user;
            }
        }
    }

    public function resetPassword($password){

        $this->password = $password;

        $this->validate();

        if(empty($this->errors)){
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'UPDATE users
                    SET password = :password_hash,
                    password_reset_hash = NULL,
                    password_reset_expires_at = NULL
                    WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue('id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        }
        return false;
    }

    public function sendActivationEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;

        $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);

        Mail::send($this->email, 'Aktywacja konta', $text, $html);
    }

    public static function activate($value)
    {
        $token = new Token($value);
        $hashed_token = $token->getHash();

        $sql = 'UPDATE users
                SET is_active = 1,
                    activation_hash = null
                WHERE activation_hash = :hashed_token';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt->execute(); 
    }

    public function updatePassword($newPassword){

        $this->password = $newPassword;

        $this->validate();

        if(empty($this->erroes)){

            $sql = 'UPDATE users
                    SET password = :password
                    WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }

    public function deleteAccount(){

       $sql = 'DELETE users, expenses, incomes, expenses_category_assigned_to_users, 
                       incomes_category_assigned_to_users, payment_methods_assigned_to_users
                FROM users
                LEFT JOIN expenses ON expenses.user_id = users.id
                LEFT JOIN incomes ON incomes.user_id = users.id
                LEFT JOIN expenses_category_assigned_to_users ON expenses_category_assigned_to_users.user_id = users.id
                LEFT JOIN incomes_category_assigned_to_users ON incomes_category_assigned_to_users.user_id = users.id
                LEFT JOIN payment_methods_assigned_to_users ON payment_methods_assigned_to_users.user_id = users.id
                WHERE users.id = :id';

     $db = static::getDB();
     $stmt = $db->prepare($sql);

     $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

     $stmt->execute();
    }
}
