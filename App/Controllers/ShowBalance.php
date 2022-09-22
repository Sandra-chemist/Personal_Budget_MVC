<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Balance;
use \App\Flash;


class ShowBalance extends Authenticated{
    public function indexAction(){
        $balance = new Balance($_POST);
        
        if(!$balance){
            Flash::addMessage('Podano nieprawidłowe dane');
            $this->redirect('/Menu/index');
        }
        View::renderTemplate('ShowBalance/index.html');
    }
}
