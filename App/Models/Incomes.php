<?php

namespace App\Models;

use PDO;
use \Core\View;

class Incomes extends \Core\Model{
  
  public $errors = [];

  public function __construct($id, $data = []){

    $this->user_id = $id;

    foreach($data as $key => $value){
            $this->$key = $value; 
     }

     if(!isset($data['category'])){
        $this->category = 'Empty';
     }
  }

  public static function addIncomeCategories($user_id){

    $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name)
            SELECT :user_id, name
            FROM incomes_category_default';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':user_id', $user_id , PDO::PARAM_INT);

    $stmt->execute();
  }

  public static function getIncomeCategories($user_id){

  $sql = 'SELECT name, id
          FROM incomes_category_assigned_to_users
          WHERE user_id = :user_id
          ORDER BY name';

  $db = static::getDB();
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

  $stmt->execute();

  return $stmt->fetchAll();
  }

  public function addIncome(){

    $this->validate();

    $incomeCategoryId = $this->getIncomeCategoryId();

    if(empty($this->errors)){

    $sql = 'INSERT INTO incomes
          (user_id,
          income_category_assigned_to_user_id,
          amount,
          date_of_income,
          income_comment)
          VALUES
              (:user_id,
              :incomeCategoryId,
              :amount, 
              :date,
              :comment)';

    $db = static::getDB();
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
    $stmt->bindValue(':incomeCategoryId', $incomeCategoryId['id'] , PDO::PARAM_INT);
    $stmt->bindValue(':amount', $this->amount , PDO::PARAM_STR);
    $stmt->bindValue(':date', $this->date , PDO::PARAM_STR);
    $stmt->bindValue(':comment', $this->comment , PDO::PARAM_STR);

    return $stmt->execute();
  }
  return false;
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
  protected function getIncomeCategoryId(){

  $sql = 'SELECT id
          FROM incomes_category_assigned_to_users
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

  public static function getUserIncomeCategorySumInAFrame($user_id, $firstDay, $lastDay){

    $sql = 'SELECT
            incomes_category_assigned_to_users.name,
            SUM(incomes.amount) AS "sum"
            FROM incomes_category_assigned_to_users
            LEFT JOIN incomes
            ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id
            WHERE incomes.date_of_income BETWEEN :first_day AND :last_day
            AND incomes.user_id = :user_id
            GROUP BY incomes_category_assigned_to_users.name
            ORDER BY incomes_category_assigned_to_users.name';
  
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':first_day', $firstDay, PDO::PARAM_STR);
    $stmt->bindValue(':last_day', $lastDay, PDO::PARAM_STR);
  
    $stmt->execute();
  
    return $stmt->fetchAll();
  }

  public static function getAllUserIncomesInATimeFrame($user_id, $firstDay, $lastDay){

    $sql = 'SELECT
            incomes_category_assigned_to_users.name,
            incomes.date_of_income,
            incomes.income_comment,
            incomes.amount,
            incomes.id
            FROM incomes_category_assigned_to_users
            LEFT JOIN incomes
            ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id
            WHERE incomes.date_of_income BETWEEN :first_day AND :last_day
            AND incomes.user_id = :user_id
            ORDER BY incomes_category_assigned_to_users.name';
  
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':first_day', $firstDay, PDO::PARAM_STR);
    $stmt->bindValue(':last_day', $lastDay, PDO::PARAM_STR);
  
    $stmt->execute();
  
    return $stmt->fetchAll();
  }

  public static function removeIncome($income_id){

    $sql = 'DELETE 
            FROM incomes
            WHERE id=:income_id';
    
    $db = static::getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':income_id', $income_id, PDO::PARAM_INT);

    $stmt->execute();
  }

}
