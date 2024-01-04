<?php

namespace App\Models;

use PDO;
use \Core\View;

class Expenses extends \Core\Model{
  
  public $errors = [];

  public function __construct($id, $data = []){

    $this->user_id = $id;

    foreach($data as $key => $value){
            $this->$key = $value; 
     }

     if(!isset($data['category'])){
        $this->category = 'Empty';
     }

     if(!isset($data['paymentMethod'])){
        $this->paymentMethod = 'Empty';
   }

  }

  public static function addExpenseCategories($user_id){

    $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name)
            SELECT :user_id, name
            FROM expenses_category_default';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':user_id', $user_id , PDO::PARAM_INT);

    $stmt->execute();
}

  public static  function addPaymentMethods($user_id){

    $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name)
            SELECT :user_id, name
            FROM payment_methods_default';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':user_id', $user_id , PDO::PARAM_INT);

    $stmt->execute();
  }

  public static function getExpenseCategories($user_id){

    $sql = 'SELECT name, id, expense_limit
            FROM expenses_category_assigned_to_users
            WHERE user_id = :user_id
            ORDER BY name';

   $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll();
  }

  public static function getPaymentMethods($user_id){

    $sql = 'SELECT name, id
            FROM payment_methods_assigned_to_users 
            WHERE user_id = :user_id
            ORDER BY name';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll();
  }

  public function validate(){

    if($this->amount == ''){
      $this->errors[] = 'Kwota jest wymagana!';
    }else if($this->amount < 0.01){
      $this->errors[] = 'Nie możesz podaj kwoty mniejszej niż 1 grosz!';
    }

    if($this->category == 'Empty'){
      $this->errors[] = 'Wybór kategorii jest wymagany!';
    }

    if($this->paymentMethod == 'Empty'){
      $this->errors[] = 'Wybór metody płatności jest wymagany!';
    }

    $minimumDate = \DateTime::createFromFormat('Y-m-d', '2000-01-01');
    $currentDate = new \DateTime(); 

    if ($this->date == '') {
      $this->errors[] = 'Data jest wymagana!';
    }else {

      $userDate = \DateTime::createFromFormat('Y-m-d', $this->date);
      $formattedUserDate = $userDate->format('Y-m-d');

      if ($formattedUserDate > $currentDate->format('Y-m-d')) {
        $this->errors[] = 'Nie możesz wprowadzić daty późniejszej niż aktualny dzień!';
      } else if ($formattedUserDate < $minimumDate->format('Y-m-d')) {
        $this->errors[] = 'Proszę wprowadzić datę późniejszą niż 01-01-2000 rok.';
      }
    }
  }

  public function addExpense(){

    $this->validate();

    $expenseCategoryId = $this->getExpenseCategoryId();
    
    $paymentMehodId = $this->getPaymentMethodId();

    if(empty($this->errors)){

      $sql = 'INSERT INTO expenses
              (user_id,
              expense_category_assigned_to_user_id,
              payment_method_assigned_to_user_id,
              amount,
              date_of_expense,
              expense_comment)
              VALUES
                  (:user_id,
                  :expenseCategoryId,
                  :paymentMehodId,
                  :amount, 
                  :date,
                  :comment)';

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':user_id', $this->user_id , PDO::PARAM_INT);
      $stmt->bindValue(':expenseCategoryId', $expenseCategoryId['id'] , PDO::PARAM_INT);
      $stmt->bindValue(':paymentMehodId', $paymentMehodId['id'] , PDO::PARAM_INT);
      $stmt->bindValue(':amount', $this->amount , PDO::PARAM_STR);
      $stmt->bindValue(':date', $this->date , PDO::PARAM_STR);
      $stmt->bindValue(':comment', $this->comment , PDO::PARAM_STR);

      return $stmt->execute();
    }

    return false;
  }

  protected function getExpenseCategoryId(){

    $sql = 'SELECT id
            FROM expenses_category_assigned_to_users
            WHERE user_id = :user_id
            AND
            name = :category';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
    $stmt->bindValue(':category', $this->category, PDO::PARAM_STR);

    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  protected function getPaymentMethodId(){

    $sql = 'SELECT id
            FROM payment_methods_assigned_to_users
            WHERE user_id = :user_id
            AND
            name = :paymentMethod';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
    $stmt->bindValue(':paymentMethod', $this->paymentMethod, PDO::PARAM_STR);

    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
  
  public static function getUserExpenseCategorySumInAFrame($user_id, $firstDay, $lastDay){

    $sql = 'SELECT
            expenses_category_assigned_to_users.name,
            SUM(expenses.amount) AS "sum"
            FROM expenses_category_assigned_to_users
            LEFT JOIN expenses
            ON expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id
            WHERE expenses.date_of_expense BETWEEN :first_day AND :last_day
            AND expenses.user_id = :user_id
            GROUP BY expenses_category_assigned_to_users.name
            ORDER BY expenses_category_assigned_to_users.name';
  
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':first_day', $firstDay, PDO::PARAM_STR);
    $stmt->bindValue(':last_day', $lastDay, PDO::PARAM_STR);
  
    $stmt->execute();
  
    return $stmt->fetchAll();
  }

  public static function getAllUserExpensesInATimeFrame($user_id, $firstDay, $lastDay){

    $sql = 'SELECT
            expenses_category_assigned_to_users.name,
            expenses.date_of_expense,
            expenses.expense_comment,
            expenses.amount,
            expenses.id,
            expenses_category_assigned_to_users.expense_limit,
            payment_methods_assigned_to_users.name AS "payment_method"
            FROM expenses_category_assigned_to_users
            LEFT JOIN expenses
            ON expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id
            LEFT JOIN payment_methods_assigned_to_users
            ON expenses.payment_method_assigned_to_user_id = payment_methods_assigned_to_users.id
            WHERE expenses.date_of_expense BETWEEN :first_day AND :last_day
            AND expenses.user_id = :user_id
            ORDER BY expenses_category_assigned_to_users.name';
  
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':first_day', $firstDay, PDO::PARAM_STR);
    $stmt->bindValue(':last_day', $lastDay, PDO::PARAM_STR);
  
    $stmt->execute();
  
    return $stmt->fetchAll();
  }

  public static function removeExpense($expense_id){

    $sql = 'DELETE 
            FROM expenses
            WHERE id=:expense_id';
    
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':expense_id', $expense_id, PDO::PARAM_INT);

    $stmt->execute();
  }

  public static function addNewCategory($user_id, $categoryName){

    $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name)
            VALUES (:user_id, :categoryName)';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);

    $stmt->execute();
  }

  public static function getLimit($userId, $categoryName){
    
    $sql = 'SELECT expense_limit
            FROM expenses_category_assigned_to_users
            WHERE user_id = :userId
            AND name = :categoryName';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);

   $stmt->execute();

   $result = $stmt->fetchColumn();

   if($result !== false){

    return $result;
   }else{
    return null;
   }
  }

  public static function getMonthlyExpenses($userId, $category, $firstDay, $lastDay){

        $sql = 'SELECT
                SUM(expenses.amount) AS "sum"
                FROM expenses_category_assigned_to_users
                JOIN expenses
                ON expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id
                WHERE expenses.date_of_expense BETWEEN :firstDay AND :lastDay
                AND expenses.user_id = :userId
                AND expenses_category_assigned_to_users.name = :category
                GROUP BY expenses_category_assigned_to_users.name
                ORDER BY expenses_category_assigned_to_users.name';

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
      $stmt->bindValue(':category', $category, PDO::PARAM_STR);
      $stmt->bindValue(':firstDay', $firstDay, PDO::PARAM_STR);
      $stmt->bindValue(':lastDay', $lastDay, PDO::PARAM_STR);

      $stmt->execute();

      $result = $stmt->fetchColumn();

      if($result !== false){

        return $result;
       }else{
        return null;
       }
  }
}
