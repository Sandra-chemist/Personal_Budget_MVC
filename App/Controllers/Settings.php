<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Category;

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
}
