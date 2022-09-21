<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\FinancialOperation;

class Income extends Authenticated
{
    public function indexAction()
    {
        View::renderTemplate('Expense/index.html');
    }

    public function createAction()
    {
        $financialOperation = new FinancialOperation($_POST);

        if ($financialOperation->saveExpense()) {
            $this->redirect('/Menu/index');
        } else {
            View::renderTemplate('Expense/index.html', [
                'financialOperation' => $financialOperation
            ]);
        }
    }
}
