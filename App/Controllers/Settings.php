<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Category;
use \App\Models\IncomeCategory;
use \App\Models\ExpenseCategory;
use \App\Flash;


class Settings extends Authenticated{
    public function indexAction(){
        View::renderTemplate('Settings/index.html');
    }

    public function incomeCategoriesAction(){
        $incomeCategories = Category::getLoggedUserIncomeCategories();
        View::renderTemplate('Settings/incomeCategories.html', [
            'incomeCategories' => $incomeCategories
        ]);
    }

    public function expenseCategoriesAction(){
        $expenseCategories = Category::getLoggedUserExpenseCategories();
        View::renderTemplate('Settings/expenseCategories.html', [
            'expenseCategories' => $expenseCategories
        ]);
    }

    public function paymentMethodsAction(){
        View::renderTemplate('Settings/paymentMethods.html');
    }

    public function createIncomeCategoryAction(){
        $incomeCategory = new IncomeCategory($_POST);

        if ($incomeCategory->addIncomeCategory()) {
            Flash::addMessage('Kategoria przychodu została poprawnie dodana!');
            $this->redirect('/Settings/index');
        } else {
            View::renderTemplate('Settings/incomeCategories.html', [
                'incomeCategory' => $incomeCategory
            ]);
        }
    }

    public function createExpenseCategoryAction(){
        $expenseCategory = new ExpenseCategory($_POST);

        if ($expenseCategory->addExpenseCategory()) {
            Flash::addMessage('Kategoria wydatku została poprawnie dodana!');
            $this->redirect('/Settings/index');
        } else {
            View::renderTemplate('Settings/expenseCategories.html', [
                'expenseCategory' => $expenseCategory
            ]);
        }
    }
}
