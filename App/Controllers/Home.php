<?php

namespace App\Controllers;

use \Core\View;     

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction(){
        \App\Mail::send('sandra.skibiszewska.programista@gmail.com', 'Test', 'To jest test', '<h1>To jest test</h1>');
        View::renderTemplate('Home/index.html');
    }
}

