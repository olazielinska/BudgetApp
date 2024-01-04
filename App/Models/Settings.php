<?php

namespace App\Models;

use PDO;
use \Core\View;

class Settings extends \Core\Model{

  public $errors = [];

  public function __construct($id, $data){

    $this->user_id = $id;

    foreach($data as $key => $value){
      $this->$key = $value; 
    }
  }

  public static function removeIncomeCategory($income_category_id){

    $sql = 'DELETE 
            incomes_category_assigned_to_users, incomes
            FROM incomes_category_assigned_to_users
            LEFT JOIN incomes ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id
            WHERE incomes_category_assigned_to_users.id = :income_category_id';
    
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':income_category_id', $income_category_id, PDO::PARAM_INT);

    $stmt->execute();
  }

  public static function removeExpenseCategory($expense_category_id){

    $sql = 'DELETE 
            expenses_category_assigned_to_users, expenses
            FROM expenses_category_assigned_to_users
            LEFT JOIN expenses ON expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id
            WHERE expenses_category_assigned_to_users.id = :expense_category_id';
    
    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':expense_category_id', $expense_category_id, PDO::PARAM_INT);

    $stmt->execute();
  }

  public static function removePaymentMethod($payment_method_id){

    $sql = 'DELETE 
            payment_methods_assigned_to_users, expenses
            FROM payment_methods_assigned_to_users
            LEFT JOIN expenses ON payment_methods_assigned_to_users.id = expenses.payment_method_assigned_to_user_id
            WHERE  payment_methods_assigned_to_users.id = :payment_method_id';
    
    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':payment_method_id', $payment_method_id, PDO::PARAM_INT);

    $stmt->execute();
  }

  public function addNewIncomeCategory(){

    $this->incomeValidate();

    if(empty($this->errors)){

    $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name)
            VALUES (:user_id, :categoryName)';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
    $stmt->bindValue(':categoryName', $this->newIncomeCategory, PDO::PARAM_STR);

    $stmt->execute();
    
    return true;
    }
    return false;
    }

    public function addNewExpenseCategory(){

      $this->expenseValidate();
  
      if(empty($this->errors)){
  
      $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name)
              VALUES (:user_id, :categoryName)';
  
      $db = static::getDB();
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
      $stmt->bindValue(':categoryName', $this->newExpenseCategory, PDO::PARAM_STR);
  
      $stmt->execute();
      
      return true;
      }else{
        return false;
      }
    }

    public function addNewPaymentMethod(){

      $this->paymentMethodValidate();
  
      if(empty($this->errors)){
  
      $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name)
              VALUES (:user_id, :paymentMethod)';
  
      $db = static::getDB();
      $stmt = $db->prepare($sql);
      $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
      $stmt->bindValue(':paymentMethod', $this->newPaymentMethod, PDO::PARAM_STR);
  
      $stmt->execute();
      
      return true;
      }else{
        return false;
      }
    }

  protected function checkIfIncomeCategoryExists(){

    $sql = 'SELECT name
            FROM incomes_category_assigned_to_users
            WHERE user_id = :user_id
            AND name = :categoryName';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
    $stmt->bindValue(':categoryName', $this->newIncomeCategory, PDO::PARAM_STR);

   $stmt->execute();

    return $stmt->fetch();
  }

  protected function checkIfExpenseCategoryExists(){

    $sql = 'SELECT name
            FROM expenses_category_assigned_to_users
            WHERE user_id = :user_id
            AND name = :categoryName';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
    $stmt->bindValue(':categoryName', $this->newExpenseCategory, PDO::PARAM_STR);

   $stmt->execute();

  return $stmt->fetch();
  }

  protected function checkIfPaymentMethodExists(){

    $sql = 'SELECT name
            FROM payment_methods_assigned_to_users
            WHERE user_id = :user_id
            AND name = :paymentMethod';

    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
    $stmt->bindValue(':paymentMethod', $this->newPaymentMethod, PDO::PARAM_STR);

   $stmt->execute();

  return $stmt->fetch();
  }

  protected function incomeValidate(){

    if($this->newIncomeCategory == ''){
      $this->errors[] = 'Podaj nazwę kategorii!';
      return false;
    }

    $this->newIncomeCategory = $this->capitalizeFirstLetter($this->newIncomeCategory);

    if(!empty($this->checkIfIncomeCategoryExists())){
        $this->errors[] = 'Taka kategoria już istnieje!';
    }
  }

  protected function expenseValidate(){
    
    if($this->newExpenseCategory == ''){
      $this->errors[] = 'Podaj nazwę kategorii!';
      return false;
    }

    $this->newExpenseCategory = $this->capitalizeFirstLetter($this->newExpenseCategory);

    if(!empty($this->checkIfExpenseCategoryExists())){
        $this->errors[] = 'Taka kategoria już istnieje!';
    }
  }

  protected function paymentMethodValidate(){
    
    if($this->newPaymentMethod == ''){
      $this->errors[] = 'Podaj nazwę kategorii!';
      return false;
    }

    $this->newPaymentMethod = $this->capitalizeFirstLetter($this->newPaymentMethod);

    if(!empty($this->checkIfPaymentMethodExists())){
        $this->errors[] = 'Taka kategoria już istnieje!';
    }
  }

  protected function capitalizeFirstLetter($categoryName){

    return ucfirst(strtolower($categoryName));
  }

  public static function editIncomeCategory($categoryId, $categoryName){

    $sql = 'UPDATE incomes_category_assigned_to_users
            SET name = :categoryName
            WHERE id = :categoryId';
  
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
  
    $stmt->execute();
  }

  public static function editExpenseCategory($categoryId, $categoryName){

    $sql = 'UPDATE expenses_category_assigned_to_users
            SET name = :categoryName
            WHERE id = :categoryId';
  
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
  
    $stmt->execute();
  }

  public static function editPaymentMethod($methodId, $methodName){

    $sql = 'UPDATE payment_methods_assigned_to_users
            SET name = :methodName
            WHERE id = :methodId';
  
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':methodId', $methodId, PDO::PARAM_INT);
    $stmt->bindValue(':methodName', $methodName, PDO::PARAM_STR);
  
    $stmt->execute();
  }

  public static function setCategoryLimit($categoryId, $expenseLimit){

    $sql = 'UPDATE expenses_category_assigned_to_users
            SET expense_limit = :expenseLimit
            WHERE id = :categoryId';

  $db = static::getDB();
  $stmt = $db->prepare($sql);

  $stmt->bindValue(':expenseLimit', $expenseLimit, PDO::PARAM_INT);
  $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);

  $stmt->execute();
  }
}
