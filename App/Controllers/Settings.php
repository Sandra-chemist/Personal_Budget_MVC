<?php

namespace App\Controllers;

use \Core\View;

class Settings extends Authenticated{
    public function indexAction(){
        View::renderTemplate('Settings/index.html');
    }

    public function incomeCategoriesAction(){
        View::renderTemplate('Settings/incomeCategories.html');
    }

    public function expenseCategoriesAction(){
        View::renderTemplate('Settings/expenseCategories.html');
    }

    public function paymentMethodsAction(){
        View::renderTemplate('Settings/paymentMethods.html');
    }
}
