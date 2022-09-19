<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\FinancialOperation;

class Income extends Authenticated{
    public function indexAction(){
        View::renderTemplate('Income/index.html');
    }

    public function createAction(){
        $financialOperation = new $FinancialOperation($_POST);

        if ($financialOperation->saveIncome()) {
            $this->redirect('/Menu/index');
        } else {
            View::renderTemplate('Income/index.html', [
                'financialOperation' => $financialOperation
            ]);
        }     

    }
}
 

