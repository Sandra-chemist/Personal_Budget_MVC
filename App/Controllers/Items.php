<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Items extends \Core\Controller
{
    protected function before(){
        $this->requireLogin();
    }
    /**
     * Items index
     *
     * @return void
     */
    public function indexAction(){
        View::renderTemplate('Items/index.html');
    }

    public function newAction(){
        $this->requireLogin();
        echo "new action";
    }

    public function showAction(){
        $this->requireLogin();
        echo "show action";
    }
}
