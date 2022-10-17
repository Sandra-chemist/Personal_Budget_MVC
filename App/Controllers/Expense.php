<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\FinancialOperation;
use \App\Flash;
use \App\Models\Category;

class Expense extends Authenticated{
    protected function before(){
        parent::before();

        $this->expenseCategories = Category::getLoggedUserExpenseCategories();
        $this->paymentMethods = Category::getLoggedUserPaymentMethods();
    }

    public function indexAction(){
        View::renderTemplate('Expense/index.html', [
            'expenseCategories' => $this->expenseCategories,
            'paymentMethods' => $this->paymentMethods
        ]);
    }

    public function createAction(){
        $financialOperation = new FinancialOperation($_POST);

        if ($financialOperation->saveExpense()) {
            Flash::addMessage('Wydatek został poprawnie dodany!');
            $this->redirect('/Expense/index');
        } else {
            View::renderTemplate('Expense/index.html', [
                'financialOperation' => $financialOperation
            ]);
        }
    }
}
