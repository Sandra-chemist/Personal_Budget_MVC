<?php

namespace App\Controllers;

use \Core\View;

class ShowBalance extends Authenticated{
    public function indexAction()
    {
        View::renderTemplate('ShowBalance/index.html');
    }
}
