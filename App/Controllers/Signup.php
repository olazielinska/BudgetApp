<?php

namespace App\Controllers;

use \Core\View;
use App\Models\User;
use \App\Models\Incomes;
use \App\Models\Expenses;

class Signup extends \Core\Controller
{
    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }

    public function createAction(){
        
        $user = new User($_POST);

        if($user->save()){

        $user_id = (User::findByEmail($user->email))->id;

        Incomes::addIncomeCategories($user_id);
        Expenses::addExpenseCategories($user_id);
        Expenses::addPaymentMethods($user_id);
   
        $user->sendActivationEmail();

        $this->redirect('/signup/success');
    }else{
        View::RenderTemplate('Signup/new.html', [
            'user' => $user
        ]);
    }
    }

    public function successAction(){
        View::RenderTemplate('Signup/success.html');
    }

    public function activateAction()
    {
        User::activate($this->route_params['token']);

        $this->redirect('/signup/activated');        
    }

    public function activatedAction()
    {
        View::renderTemplate('Signup/activated.html');
    }
}
