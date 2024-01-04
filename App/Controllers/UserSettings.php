<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\User;
use \App\Models\Settings;

class UserSettings extends Authenticated{

  protected function before(){

    parent::before();
    $this->user = Auth::getUser();
  }

  public function SettingsAction(){

    View::renderTemplate('UserSettings/settings.html',
          ['user' => $this->user]);
  }

  public function editPasswordAction(){

    var_dump($_POST);

    if($this->user->updatePassword($_POST['newPassword'])){

      Flash::addMessage('Hasło zostało zmienione!');
    }
      else{

      Flash::addMessage('Nie udało się zmienić hasła!');
    }

    View::renderTemplate('UserSettings/settings.html',
                        ['user' => $this->user]);
  }

  public function changeCategoryLimit(){

    var_dump($_POST);
  }

  public function editIncomeCategoryAction(){

    var_dump($_POST);

    Settings::editIncomeCategory($_POST['categoryId'], $_POST['categoryName']);

    View::renderTemplate('UserSettings/settings.html',
                          ['user' => $this->user]);
    
  }

  public function editExpenseCategoryAction(){

    Settings::editExpenseCategory($_POST['categoryId'], $_POST['categoryName']);

    View::renderTemplate('UserSettings/settings.html',
    ['user' => $this->user]);
    
  }

  public function editPaymentMethodAction(){

    Settings::editPaymentMethod($_POST['methodId'], $_POST['paymentMethodName']);

    View::renderTemplate('UserSettings/settings.html',
    ['user' => $this->user]);

  }

  public function deleteAction(){

    $this->user->deleteAccount();

    Flash::addMessage('Konto zostało usunięte!');

    $this->redirect('/');
  }

  public function removeIncomeCategoryAction(){

    Settings::removeIncomeCategory($_POST['categoryId']);

    View::renderTemplate('UserSettings/settings.html',
                          ['user' => $this->user]);
  }

  public function removeExpenseCategoryAction() {

    Settings::removeExpenseCategory($_POST['categoryId']);

    View::renderTemplate('UserSettings/settings.html',
                          ['user' => $this->user]);
    
  }
  public function removePaymentMethodAction(){

    Settings::removePaymentMethod($_POST['methodId']);

    View::renderTemplate('UserSettings/settings.html',
                          ['user' => $this->user]);
  }

  public function addNewIncomeCategoryAction(){

      $newIncomeCategory = new Settings($this->user->id, $_POST);

      $addNewIncomeCategory = $newIncomeCategory->addNewIncomeCategory();

    if($addNewIncomeCategory){
      Flash::addMessage('Dodano nową kategorię!');
    }else{  
      Flash::addMessage('Nie udało się dodać nowej kategorii!');
    }

    View::RenderTemplate('UserSettings/settings.html', [
      'newIncomeCategory' => $newIncomeCategory,
      'user' => $this->user]);
  }
  

  public function addNewExpenseCategoryAction(){

      $newExpenseCategory = new Settings($this->user->id, $_POST);

      $addNewExpenseCategory = $newExpenseCategory->addNewExpenseCategory();

    if($addNewExpenseCategory){
      Flash::addMessage('Dodano nową kategorię!');
    }else{  
      Flash::addMessage('Nie udało się dodać nowej kategorii!');
    }

    View::RenderTemplate('UserSettings/settings.html', [
      'newExpenseCategory' => $newExpenseCategory,
      'user' => $this->user]);
  }

  public function addNewPaymentMethodAction(){

      $newPaymentMethod = new Settings($this->user->id, $_POST);

      $addNewPaymentMethod = $newPaymentMethod->addNewPaymentMethod();

    if($addNewPaymentMethod){
      Flash::addMessage('Dodano nową metodę płatności!');
    }else{  
      Flash::addMessage('Nie udało się dodać nowej metody płatności!');
    }

    View::RenderTemplate('UserSettings/settings.html', [
      'newPaymentMethod' => $newPaymentMethod,
      'user' => $this->user]);
  }

  public function setCategoryLimitAction(){

    Settings::setCategoryLimit($_POST['categoryId'],$_POST['categoryLimit']);

    View::renderTemplate('UserSettings/settings.html',
    ['user' => $this->user]);

  }
}