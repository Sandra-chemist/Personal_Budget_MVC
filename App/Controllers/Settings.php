<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Category;
use \App\Models\IncomeCategory;
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
        View::renderTemplate('Settings/expenseCategories.html');
    }

    public function paymentMethodsAction(){
        View::renderTemplate('Settings/paymentMethods.html');
    }

    public function createAction(){
        $incomeCategory = new IncomeCategory($_POST);

        if ($incomeCategory->addIncomeCategory()) {
            Flash::addMessage('Kategoria została poprawnie dodana!');
            $this->redirect('/Settings/index');
        } else {
            View::renderTemplate('Settings/incomeCategories.html', [
                'incomeCategory' => $incomeCategory
            ]);
        }
    }
}
