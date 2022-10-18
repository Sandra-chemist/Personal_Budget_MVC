<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Category;
use \App\Models\IncomeCategory;
use \App\Models\ExpenseCategory;
use \App\Flash;
use \App\Models\PaymentMethod;


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
        $paymentMethods = Category::getLoggedUserPaymentMethods();
        View::renderTemplate('Settings/paymentMethods.html', [
            'paymentMethods' => $paymentMethods
        ]);
    }

    public function createIncomeCategoryAction(){
        $incomeCategory = new IncomeCategory($_POST);

        if ($incomeCategory->addIncomeCategory()) {
            Flash::addMessage('Kategoria przychodu została poprawnie dodana!');
            $this->redirect('/Settings/incomeCategories');
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
            $this->redirect('/Settings/expenseCategories');
        } else {
            View::renderTemplate('Settings/expenseCategories.html', [
                'expenseCategory' => $expenseCategory
            ]);
        }
    }

    public function createPaymentMethodAction(){
        $paymentMethod = new PaymentMethod($_POST);

        if ($paymentMethod->addPaymentMethod()) {
            Flash::addMessage('Kategoria sposobu płątności została poprawnie dodana!');
            $this->redirect('/Settings/paymentMethods');
        } else {
            View::renderTemplate('Settings/paymentMethods.html', [
                'paymentMethod' => $paymentMethod
            ]);
        }
    }

    public function editIncomeCategoryAction(){
        $oldCategory = $_POST['old_category'];
        $incomeCategory = new IncomeCategory($_POST);
      

        if ($incomeCategory->editIncomeCategory($oldCategory)) {
            Flash::addMessage('Nazwa kategorii została poprawnie zmieniona!');
            $this->redirect('/Settings/incomeCategories');
        } else {
            View::renderTemplate('Settings/incomeCategories.html', [
                'incomeCategory' => $incomeCategory
            ]);
        }
    }
}
