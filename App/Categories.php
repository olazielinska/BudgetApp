<?php

namespace App;

use \App\Auth;
use \App\Models\Incomes;
use \App\Models\Expenses;

class Categories{

  private static function getUser(){
    return Auth::getUser();
  }

  public static function getIncomes(){
    $user = self::getUser();

    if($user){

    return Incomes::getIncomeCategories($user->id);}
  }

  public static function getExpenses(){
      $user = self::getUser();

    if($user){

    return Expenses::getExpenseCategories($user->id);}
  }

  public static function getPayment(){
      $user = self::getUser();

  if($user){

  return Expenses::getPaymentMethods($user->id);}
}

}