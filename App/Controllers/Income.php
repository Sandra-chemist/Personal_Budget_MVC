<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\FinancialOperation;
use \App\Flash;
use App\Models\Category;

class Income extends Authenticated{
    protected function before(){
        parent::before();

        $this->incomeCategories = Category::getLoggedUserIncomeCategories();
    }
    public function indexAction(){
        View::renderTemplate('Income/index.html', [
            'incomeCategories' => $this->incomeCategories
        ]);
    }

    public function createAction(){
        $financialOperation = new FinancialOperation($_POST);

        if ($financialOperation->saveIncome()) {
            Flash::addMessage('Przychód został poprawnie dodany!');
            $this->redirect('/Income/index');
        } else {
            View::renderTemplate('Income/index.html', [
                'financialOperation' => $financialOperation
            ]);
        }     

    }
}
 

