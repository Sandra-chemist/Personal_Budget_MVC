<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;

class Profile extends Authenticated{
    public function showAction(){
        View::renderTemplate('Profile/show.html', [
            'user' => Auth::getUser()
        ]);
    }

    public function editAction(){
        View::renderTemplate('Profile/edit.html', [
            'user' => Auth::getUser()
        ]);
    }

    public function updateAction(){
        $user = Auth::getUser();

        if ($user->updateProfile($_POST)){
            Flash::addMessage('Zmiany zostały zapisane');
            $this->redirect('/profile/show');
        } else{
            View::renderTemplate('Profile/edit.html', [
                'user' => $user
            ]);
        }
    }
}