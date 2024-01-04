<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Date;
use \App\Models\User;
use \App\Models\Incomes;
use \App\Models\Expenses;

class Balance extends Authenticated
{
    protected function before(){

        parent::before();
        $this->user = Auth::getUser();
    }
    
    private function getIncomeBalance($date){

        $incomeBalance = [];
            
        $incomeCategorySum = Incomes::getUserIncomeCategorySumInAFrame($this->user->id, $date['startDate'],$date['endDate']);

        $allIncomes =  Incomes::getAllUserIncomesInATimeFrame($this->user->id, $date['startDate'],$date['endDate']);
            
        $incomeBalance['incomeCategorySum'] = $incomeCategorySum;
        $incomeBalance['allIncomes'] = $allIncomes;

        return $incomeBalance;
    }

    private function getExpenseBalance($date){

        $expenseBalance = [];

        $expenseCategorySum = Expenses::getUserExpenseCategorySumInAFrame($this->user->id, $date['startDate'],$date['endDate']);

        $allExpenses =  Expenses::getAllUserExpensesInATimeFrame($this->user->id, $date['startDate'],$date['endDate']);
            
        $expenseBalance['expenseCategorySum'] = $expenseCategorySum;
        $expenseBalance['allExpenses'] = $allExpenses;

        return $expenseBalance;
    }
    
    public function balanceAction(){

        View::renderTemplate('Balance/balance.html');
    }

    public function currentMonthAction()
    {
        $date = Date::getCurrentMonth();
        
        $currentIncomeBalance = self::getIncomeBalance($date);
        $currentExpenseBalance = self::getExpenseBalance($date);

        View::renderTemplate('Balance/balance.html', 
        ['allIncomes' => $currentIncomeBalance['allIncomes'],
        'incomeCategorySum' => $currentIncomeBalance['incomeCategorySum'],
        'allExpenses' => $currentExpenseBalance['allExpenses'],
        'expenseCategorySum' => $currentExpenseBalance['expenseCategorySum'],
        'date' => $date]);
     }

     public function previousMonthAction(){

        $date = Date::getPreviousMonth();

        $previousMonthIncomeBalance = self::getIncomeBalance($date);
        $previousMonthExpenseBalance = self::getExpenseBalance($date);

        View::renderTemplate('Balance/balance.html', 
        ['allIncomes' => $previousMonthIncomeBalance['allIncomes'],
        'incomeCategorySum' => $previousMonthIncomeBalance['incomeCategorySum'],
        'allExpenses' => $previousMonthExpenseBalance['allExpenses'],
        'expenseCategorySum' => $previousMonthExpenseBalance['expenseCategorySum'],
        'date' => $date]);
     }

     public function previousYearAction(){

        $date = Date::getPreviousYear();

        $previousYearIncomeBalance = self::getIncomeBalance($date);
        $previousYearExpenseBalance = self::getExpenseBalance($date);

        View::renderTemplate('Balance/balance.html', 
        ['allIncomes' => $previousYearIncomeBalance['allIncomes'],
        'incomeCategorySum' => $previousYearIncomeBalance['incomeCategorySum'],
        'allExpenses' => $previousYearExpenseBalance['allExpenses'],
        'expenseCategorySum' => $previousYearExpenseBalance['expenseCategorySum'],
        'date' => $date]);
     }

     public function customDateAction(){

        if(!empty($_POST)){
            $date = ($_POST);

        $customIncomeBalance = self::getIncomeBalance($date);
        $customExpenseBalance = self::getExpenseBalance($date);

        View::renderTemplate('Balance/balance.html', 
        ['allIncomes' => $customIncomeBalance['allIncomes'],
        'incomeCategorySum' => $customIncomeBalance['incomeCategorySum'],
        'allExpenses' => $customExpenseBalance['allExpenses'],
        'expenseCategorySum' => $customExpenseBalance['expenseCategorySum'],
        'date' => $date]);
        }
        else{
            self::balance();
        }
     }

     public function removeIncomeAction(){

        Incomes::removeIncome($_POST['income_id']);

        $this->redirect(Auth::getPreviousPage());
     }

     public function removeExpenseAction(){

        Expenses::removeExpense($_POST['expense_id']);

        $this->redirect(Auth::getPreviousPage());
     }
}

