<?php

namespace App\Controllers;

use \Core\View;

class Menu extends Authenticated{
    public function mainAction(){
        View::renderTemplate('Menu/show_menu.html');
    }
}