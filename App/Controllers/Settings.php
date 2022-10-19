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
            Flash::addMessage('Sposób płatności został poprawnie dodany!');
            $this->redirect('/Settings/paymentMethods');
        } else {
            View::renderTemplate('Settings/paymentMethods.html', [
                'paymentMethod' => $paymentMethod
            ]);
        }
    }

    public function editIncomeCategoryAction(){
        $oldCategory = $_POST['old_name_category'];
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

     public function deleteIncomeCategoryAction(){
        $oldCategory = $_POST['old_name_category'];
        $oldIdCategory = $_POST['old_id_category'];
        $incomeCategory = new IncomeCategory($_POST);
      
        if ($incomeCategory->deleteIncomeCategory($oldCategory) && $incomeCategory->deleteIncomesAssignedToDeletedCategory($oldIdCategory)) {
            Flash::addMessage('Kategoria oraz przychody tej kategorii zostały usunięte!');
            $this->redirect('/Settings/incomeCategories');
        } else {
            View::renderTemplate('Settings/incomeCategories.html', [
                'incomeCategory' => $incomeCategory
            ]);
        }
    }

    public function editExpenseCategoryAction(){
        $oldCategory = $_POST['old_name_category'];
        $expenseCategory = new ExpenseCategory($_POST);

        if ($expenseCategory->editExpenseCategory($oldCategory)) {
            Flash::addMessage('Nazwa kategorii została poprawnie zmieniona!');
            $this->redirect('/Settings/expenseCategories');
        } else {
            View::renderTemplate('Settings/expenseCategories.html', [
                'expenseCategory' => $expenseCategory
            ]);
        }
    }

    public function deleteExpenseCategoryAction(){
        $oldCategory = $_POST['old_name_category'];
        $oldIdCategory = $_POST['old_id_category'];
        $expenseCategory = new ExpenseCategory($_POST);

        if ($expenseCategory->deleteExpenseCategory($oldCategory) && $expenseCategory->deleteExpensesAssignedToDeletedCategory($oldIdCategory)) {
            Flash::addMessage('Kategoria oraz wydatki tej kategorii zostały usunięte!');
            $this->redirect('/Settings/expenseCategories');
        } else {
            View::renderTemplate('Settings/expenseCategories.html', [
                'expenseCategory' => $expenseCategory
            ]);
        }
    }

    public function editPaymentMethodAction(){
        $oldCategory = $_POST['old_name_category'];
        $paymentMethod = new PaymentMethod($_POST);

        if ($paymentMethod->editPaymentMethod($oldCategory)) {
            Flash::addMessage('Nazwa sposobu płatności została poprawnie zmieniona!');
            $this->redirect('/Settings/paymentMethods');
        } else {
            View::renderTemplate('Settings/paymentMethods.html', [
                'paymentMethod' => $paymentMethod
            ]);
        }
    }

    public function deletePaymentMethodAction(){
        $oldCategory = $_POST['old_name_category'];
        $oldIdCategory = $_POST['old_id_category'];
        $paymentMethod = new PaymentMethod($_POST);

        if ($paymentMethod->deletePaymentMethod($oldCategory) && $paymentMethod->deleteExpenseAssignedToDeletedPaymentMethod($oldIdCategory)) {
            Flash::addMessage('Sposób płatności oraz wydatki przypisane do tej kategorii zostały usunięte!');
            $this->redirect('/Settings/paymentMethods');
        } else {
            View::renderTemplate('Settings/paymentMethods.html', [
                'paymentMethody' => $paymentMethod
            ]);
        }
    }
}
