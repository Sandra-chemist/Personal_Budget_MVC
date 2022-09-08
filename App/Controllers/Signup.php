<?php

namespace App\Controllers;

use \Core\View;

/*  Signup controller 
    * PHP version 9.1
    */

class Signup extends \Core\Controller{
    /*  
        Show the signup page
        @return void
        */
    public function newAction(){
        View::renderTemplate('Signup/new.html');
    }
}