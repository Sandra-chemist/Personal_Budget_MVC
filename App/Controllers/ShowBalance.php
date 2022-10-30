<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Balance;
use \App\Flash;


class ShowBalance extends Authenticated{
   public function indexAction(){
       $balance = new Balance($_POST);
        
        if(!$balance){
            Flash::addMessage('Podano nieprawidłowe dane.');
            $this->redirect('/Menu/index');
        }
        View::renderTemplate('ShowBalance/index.html');
    }

    public function currentMonthAction(){
        $currentMonth = new Balance();
        $currentMonth->getCurrentMonthData();

        View::renderTemplate('ShowBalance/index.html', [
            'balance' => $currentMonth
         ]);
    }

    public function previousMonthAction(){
        $previousMonth = new Balance();
        $previousMonth->getPreviousMonthData();

        View::renderTemplate('ShowBalance/index.html', [
            'balance' => $previousMonth
        ]);
    }

    public function currentYearAction(){
        $currentYear = new Balance();
        $currentYear->getCurrentYearData();

        View::renderTemplate('ShowBalance/index.html', [
            'balance' => $currentYear
        ]);
    }

    public function customPeriodAction(){
        $customPeriod = new Balance($_POST);
        $customPeriod->getcustomPeriodData();

        View::renderTemplate('ShowBalance/index.html', ['balance' => $customPeriod]);
    }
}

