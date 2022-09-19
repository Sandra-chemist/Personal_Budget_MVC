<?php

namespace App\Controllers;

use \Core\View;

class Menu extends Authenticated{
    public function indexAction(){
        View::renderTemplate('Menu/index.html');
    }
}