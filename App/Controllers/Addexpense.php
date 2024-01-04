<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Models\Expenses;
use \App\Auth;
use \App\Flash;
use \App\Date;

class Addexpense extends Authenticated
{
    public function newAction()
    {
        View::renderTemplate('AddExpense/new.html');
    }
    
    public function addAction(){

        $user = self::getUser();

        if($user){

            $expenses = new Expenses($user->id, $_POST);
            
            $addExpense = $expenses->addExpense();

            if($addExpense){
      
                Flash:: addMessage('Dodano wydatek!');

            }else{
                Flash::addMessage('Nie udało się dodać wydatku.');
            }
        }
        
        View::RenderTemplate('AddExpense/new.html', [
            'expenses' => $expenses
    ]);
    }

    public function limitAction (){
        
        $user = self::getUser();

        $user_id = $user->id;
        $category = $this->route_params['category'];
        
        echo json_encode(Expenses::getLimit($user_id, $category), JSON_UNESCAPED_UNICODE);
    }

    public function monthlyExpensesAction(){
        $user = self::getUser();

        $user_id = $user->id;
        $category = $this->route_params['category'];
        $date = $this->route_params['date'];

        $firstAndLastDayOfMonth = Date::getFirstAndLastDayOfMonth($date); 
        
         echo json_encode(Expenses::getMonthlyExpenses($user_id, $category, $firstAndLastDayOfMonth['firstDay'], $firstAndLastDayOfMonth['lastDay']), JSON_UNESCAPED_UNICODE);
    }
}
