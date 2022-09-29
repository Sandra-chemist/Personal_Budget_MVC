<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\FinancialOperation;
use \App\Flash;

class Income extends Authenticated{
    public function indexAction(){
        View::renderTemplate('Income/index.html');
    }

    public function createAction(){
        $financialOperation = new FinancialOperation($_POST);

        if ($financialOperation->saveIncome()) {
            Flash::addMessage('Przychód został dodany poprawnie!');
            $this->redirect('/Menu/index');
        } else {
            View::renderTemplate('Income/index.html', [
                'financialOperation' => $financialOperation
            ]);
        }     

    }
}
 

