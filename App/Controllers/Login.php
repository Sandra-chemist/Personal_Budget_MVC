<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

/**
 * Login controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller{
   
   public function newAction(){
      View::renderTemplate('Login/new.html');
   }

   public function createAction(){
      $user = User::authenticate($_POST['email'], $_POST['password']);

      if ($user) {

         Auth::login($user);

         Flash::addMessage('Zalogowano poprawnie!');

         $this->redirect(Auth::getReturnToPage());
      } else {

         Flash::addMessage('Logowanie nie powiodło się, proszę spróbuj ponownie.');

         View::renderTemplate('Login/new.html', [
            'email' => $_POST['email'],
         ]);
      }
   }

   public function destroyAction(){
      Auth::logout();

      $this->redirect('/login/show-logout-message');
   }

   public function showLogoutMessageAction(){
      Flash::addMessage('Wylogowanie powiodło się.');

      $this->redirect('/');
   }
}
