<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Models\Incomes;
use \App\Auth;
use \App\Flash;

class Addincome extends Authenticated
{
    public function newAction()
    {   
        View::renderTemplate('AddIncome/new.html');
    }

    public function addAction(){

        $user = self::getUser();

        if($user){

            $incomes = new Incomes($user->id, $_POST);
            
            $addIncome = $incomes->addIncome();

            if($addIncome){
      
                Flash:: addMessage('Dodano przychód!');

            }else{
                Flash::addMessage('Nie udało się dodać przychodu.');
            }
        }
        
        View::RenderTemplate('AddIncome/new.html', [
            'incomes' => $incomes
    ]);
    }
}
